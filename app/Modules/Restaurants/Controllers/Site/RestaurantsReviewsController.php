<?php

namespace App\Modules\Restaurants\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;
use App\Modules\Orders\Repositories\OrdersRepository;

class RestaurantsReviewsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'restaurantsReviews';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        // dd('sdsd');
        $options = [
            'restaurant' => (int) $request->restaurant,
            'orderId' => (int) $request->orderId,
            'sortRating' => $request->sortRating,
            'highestRating' => $request->highestRating,
            'lowestRating' => $request->lowestRating,
            'latest' => $request->latest,
            'oldest' => $request->oldest,
            'sort' => $request->sort,
            'published' => true,
            'id' => $request->id,
        ];

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'records' => $this->repository->list($options),
            'restaurant' => $this->restaurantsRepository->get((int) $request->restaurant),
            'paginationInfo' => $this->repository->getPaginateInfo(),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function show($id, Request $request)
    {
        return $this->success([
            'record' => $this->repository->get($id),
        ]);
    }

    public function store(Request $request)
    {
        // dd('sdsd');
        $validator = $this->scan($request);

        if ($validator->passes()) {
            $order = $this->ordersRepository->getModel($request->orderId);

            $restaurant = $this->restaurantsRepository->getModel($request->restaurantId);

            if (!$restaurant) {
                return $this->badRequest(trans('products.notFound'));
            }

            if (!$order) {
                return $this->badRequest(trans('auth.invalidData'));
            }
            if (!in_array($order->status, [
                OrdersRepository::COMPLETED_STATUS,
                OrdersRepository::REQUEST_RETURNING_STATUS,
                OrdersRepository::REQUEST_RETURNING_PENDING_STATUS,
                OrdersRepository::REQUEST_RETURNING_ACCEPTED_STATUS,
                OrdersRepository::REQUEST_RETURNING_REJECTED_STATUS,
                OrdersRepository::RETURNED_STATUS,
                OrdersRepository::PARTIALLY_RETURNED_STATUS,
                OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS,
            ])) {
                return $this->badRequest(trans('errors.cannotRateProduct'));
            }

            if ($this->repository->isRating($request->orderId, $request->restaurantId)) {
                return $this->badRequest(trans('errors.alreadyRatedRestaurant'));
            }

            $this->repository->create($request);

            $order = $this->ordersRepository->wrap($order->fresh());

            return $this->success([
                'succses' => true,
                'record' => $order,
            ]);
        } else {
            return $this->badRequest($validator->errors());
        }
    }

    /**
     * Determine whether the passed values are valid
     *
     * @param Request $request
     * @return mixed
     */
    protected function scan(Request $request)
    {
        return Validator::make($request->all(), [
            "orderId" => 'required',
            "restaurantId" => 'required',
            // "storeId" => 'required',
            "rate" => 'required',
            // "deliverySpeed" => 'required',
        ]);
    }
}
