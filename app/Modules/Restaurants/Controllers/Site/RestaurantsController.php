<?php

namespace App\Modules\Restaurants\Controllers\Site;

use Illuminate\Http\Request;
use App\Modules\General\Helpers\Visitor;
use App\Modules\Categories\Resources\Category;
use HZ\Illuminate\Mongez\Managers\ApiController;

class RestaurantsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'restaurants';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $customer = user();
        if (!$customer) {
            $customer = $this->guestsRepository->getByModel('customerDeviceId', Visitor::getDeviceId());
        }

        // if (! $customer->location) {
        //     return $this->success([
        //         'records' => []
        //     ]);
        // }

        $options = [
            'location' => $customer->location,
            'countItems' => true,
            'countProductsDiet' => true,

        ];

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        if (user()) {
            $healthyData = $this->healthyDatasRepository->getByModel('customerId', $customer->id);
        } else {
            $healthyData = $this->healthyDatasRepository->getByModel('customerDeviceId', Visitor::getDeviceId());
        }

        return $this->success([
            'records' => $this->repository->listPublished($options),
            'cartCount' => $this->cartRepository->countCart(),
            'paginationInfo' => $this->repository->getPaginateInfo(),
            'returnOrderStatus' => $this->settingsRepository->getSetting('ReturnedOrder', 'returnSystemStatus'),
            'getLastOrderIsNotReview' => $this->repository->getLastOrderIsNotReview($customer) ?? null,
            'isHealthyData' => ($healthyData) ? true : false,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function show($id, Request $request)
    {
        if (!$this->repository->has($id)) {
            return $this->notFound(trans('response.notFound'));
        }
        Category::disable('image');

        $productBestSeller = $this->productsRepository->published([
            'bestSeller' => true,
            'type' => 'food',
            'restaurant' => $id,
        ]);
        // dd($productBestSeller);
        // $aRestaurantThatHasAProduct = $this->productsRepository->published([
        //     'type' => 'food',
        //     'restaurant' => $id,
        // ]);
        // dd($aRestaurantThatHasAProduct->count());
        $bestSeller = [
            'name' => trans('general.bestSeller'),
            'id' => 0,
            'type' => 'food',
            'color' => '#258745',
            'products' => $productBestSeller,
        ];

        $categories = $this->repository->getCategoryForRestaurants($id);

        if ($productBestSeller->count() > 0) {
            // add best seller on the fly as part of categories
            array_unshift($categories, $bestSeller);
        }


        return $this->success([
            'restaurant' => $this->repository->get($id),
            'categoriesMenu' => $this->categoriesRepository->wrapMany($categories),
            'cartCount' => $this->cartRepository->countCart(),
        ]);
    }

    /**
     * Method restaurants
     *
     * @param Request $request
     * restaurants
     * @return array
     */
    public function update($id, Request $request)
    {
        if ($this->repository->get($id)) {
            $updateSection = $this->repository->update($id, $request);

            return $this->success([
                'record' => $updateSection,
            ]);
        }

        return $this->badRequest(trans('errors.notFound'));
    }

    /**
     * Method checkDistance
     *
     * @param Request $request
     *
     * @return void
     */
    public function checkDistance(Request $request)
    {
        return $this->success([
            'record' => ($this->repository->checkDistance($request)) ? true : false,
            'kmAdmin' => $this->settingsRepository->getSetting('restaurant', 'searchArea') ?: 50 . ' km',
            'betweenTheTwoPoints' => $this->repository->betweenTheTwoPoints($request),
        ]);
    }

    /**
     * get Seller's getMyRestaurants Info
     */
    public function getMyRestaurants(Request $request)
    {
        $user = user();

        // if ($user->accountType() != 'restaurantManager') {
        //     return $this->unauthorized(trans('auth.unauthorized'));
        // }
        // dd($user->restaurant['id']);
        $restaurantId = $user->restaurant['id'];

        return $this->success([
            'record' => $this->repository->get($restaurantId),
        ]);
    }

    /**
     * Update getMyRestaurants's getMyRestaurants Info
     */
    public function updateMyRestaurants(Request $request)
    {
        $user = user();

        // if ($user->accountType() != 'StoreManager') {
        //     return $this->unauthorized(trans('auth.unauthorized'));
        // }

        $restaurantId = $user->restaurant['id'];
        $this->repository->update($restaurantId, $request->all());

        return $this->success([
            'record' => $this->repository->get($restaurantId),
        ]);
    }
}
