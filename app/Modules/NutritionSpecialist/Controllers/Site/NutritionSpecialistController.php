<?php

namespace App\Modules\NutritionSpecialist\Controllers\Site;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Modules\General\Helpers\Visitor;
use HZ\Illuminate\Mongez\Managers\ApiController;

class NutritionSpecialistController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'nutritionSpecialists';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $customer = user();
        if (!$customer) {
            $customer = $this->guestsRepository->getByModel('customerDeviceId', Visitor::getDeviceId());
        }
        $options = [
            'location' => $customer->location,
        ];

        return $this->success([
            'records' => $this->repository->listPublished($options),
            'paginationInfo' => $this->repository->getPaginateInfo(),

        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function show($id, Request $request)
    {
        $nutritionSpecialist = $this->repository->get($id);

        $options = [
            'nutritionSpecialistId' => (int) $id,
        ];

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'record' => $nutritionSpecialist,
            'reviews' => $this->nutritionSpecialistReviewsRepository->list($options),
        ]);
    }

    /**
     * Method schedule
     *
     * @param $id $id
     * @param Request $request
     *
     * @return void
     */
    public function schedule($id, Request $request)
    {
        $nutritionSpecialists = $this->nutritionSpecialistMangersRepository->get($id);

        if (!$nutritionSpecialists) {
            return $this->badRequest(trans('nutritionSpecialist.notfound'));
        }

        $nutritionSpecialists = $this->repository->schedule($nutritionSpecialists);

        return $this->success([
            'nutritionSpecialists' => $nutritionSpecialists,
        ]);
    }

    /**
     * Method reviewSchedule
     *
     * @param Request $request
     *
     * @return void
     */
    public function reviewSchedule(Request $request)
    {
        if (!$this->nutritionSpecialistMangersRepository->has($request->nutritionSpecialists)) {
            return $this->notFound(trans('response.notFound'));
        }
        $nutritionSpecialists = $this->nutritionSpecialistMangersRepository->get($request->nutritionSpecialists);

        $dayDate = date("Y-m-d", strtotime($request->data));
        $dayText = Carbon::parse($dayDate)->translatedFormat('l');
        $dateText2 = Carbon::parse($dayDate)->translatedFormat('F d');
        $month = Carbon::parse($dayDate)->translatedFormat('F');
        $dayNumber = Carbon::parse($dayDate)->translatedFormat('d');
        $time = $request->time;
        $totals = [];

        $totals[] = [
            'text' => trans('orders.reviewSchedule'),
            'price' => $nutritionSpecialists['nutritionSpecialist']['finalPrice'],
            'priceText' => trans('orders.price', ['value' => $nutritionSpecialists['nutritionSpecialist']['finalPrice']]),
            'type' => 'reviewSchedule',
        ];
        // $totals[] = [
        //     'text' => trans('orders.reviewScheduleMun'),
        //     'price' => $nutritionSpecialists['nutritionSpecialist']['finalPrice'],
        //     'priceText' => trans('orders.price', ['value' => $nutritionSpecialists['nutritionSpecialist']['finalPrice']]),
        //     'type' => 'reviewScheduleMun',
        // ];
        $user = user();


        if ($user && $user->walletBalance <= $nutritionSpecialists['nutritionSpecialist']['finalPrice']) {
            $totals[] = [
                'text' => trans('orders.walletTextLang'),
                'price' => $nutritionSpecialists['nutritionSpecialist']['finalPrice'],
                'priceText' => trans('orders.price', ['value' => $nutritionSpecialists['nutritionSpecialist']['finalPrice']]),
                'type' => 'walletMinus',
            ];

            if ($user['group']) {
                $specialDiscount = (($nutritionSpecialists['nutritionSpecialist']['finalPrice'] * $user['group']['specialDiscount']) / 100);
                if ($user && $user['group']['specialDiscount']) {
                    $totals[] = [
                        'text' => trans('cart.specialDiscount'),
                        'price' => $specialDiscount,
                        'priceText' => trans('cart.price', ['value' => $specialDiscount]),
                        'type' => 'specialDiscount',
                    ];
                }
                $totals[] = [
                    'text' => trans('orders.finalPriceTextLang'),
                    'price' => $nutritionSpecialists['nutritionSpecialist']['finalPrice'] - $specialDiscount,
                    'priceText' => trans('orders.price', ['value' => $nutritionSpecialists['nutritionSpecialist']['finalPrice'] - $specialDiscount]),
                    'type' => 'finalPriceText',
                ];
            } else {
                $totals[] = [
                    'text' => trans('orders.finalPriceTextLang'),
                    'price' => $nutritionSpecialists['nutritionSpecialist']['finalPrice'],
                    'priceText' => trans('orders.price', ['value' => $nutritionSpecialists['nutritionSpecialist']['finalPrice']]),
                    'type' => 'finalPriceText',
                ];
            }
        } else {
            if ($user['group']) {
                $specialDiscount = (($nutritionSpecialists['nutritionSpecialist']['finalPrice'] * $user['group']['specialDiscount']) / 100);
                if ($user && $user['group']['specialDiscount']) {
                    $totals[] = [
                        'text' => trans('cart.specialDiscount'),
                        'price' => $specialDiscount,
                        'priceText' => trans('cart.price', ['value' => $specialDiscount]),
                        'type' => 'specialDiscount',
                    ];
                }
                $totals[] = [
                    'text' => trans('orders.finalPriceTextLang'),
                    'price' => $nutritionSpecialists['nutritionSpecialist']['finalPrice'] - $specialDiscount,
                    'priceText' => trans('orders.price', ['value' => $nutritionSpecialists['nutritionSpecialist']['finalPrice'] - $specialDiscount]),
                    'type' => 'finalPriceText',
                ];
            } else {
                $totals[] = [
                    'text' => trans('orders.finalPriceTextLang'),
                    'price' => $nutritionSpecialists['nutritionSpecialist']['finalPrice'],
                    'priceText' => trans('orders.price', ['value' => $nutritionSpecialists['nutritionSpecialist']['finalPrice']]),
                    'type' => 'finalPriceText',
                ];
            }
        }





        return $this->success([
            'nutritionSpecialists' => $nutritionSpecialists,
            'dayText' => $dayText,
            'time' => $time,
            'dateText2' => $dateText2,
            'month' => $month,
            'dayNumber' => $dayNumber,
            'totals' => $totals,
        ]);
    }

    /**
     * get Seller's getMyRestaurants Info
     */
    public function getMyNutrition(Request $request)
    {
        $user = user();
        // dd($user);
        // if ($user->accountType() != 'restaurantManager') {
        //     return $this->unauthorized(trans('auth.unauthorized'));
        // }
        // dd($user->restaurant['id']);
        $nutritiontId = $user->nutritionSpecialist['id'];

        return $this->success([
            'record' => $this->repository->get($nutritiontId),
        ]);
    }

    /**
     * Update getMyRestaurants's getMyRestaurants Info
     */
    public function updateMyNutrition(Request $request)
    {
        $user = user();

        // if ($user->accountType() != 'StoreManager') {
        //     return $this->unauthorized(trans('auth.unauthorized'));
        // }

        $nutritiontId = $user->nutritionSpecialist['id'];
        $this->repository->update($nutritiontId, $request->all());

        return $this->success([
            'record' => $this->repository->get($nutritiontId),
        ]);
    }

    /**
     * Method schedule
     *
     * @param $id $id
     * @param Request $request
     *
     * @return void
     */
    public function scheduleNutrition($id)
    {
        $nutritionSpecialists = $this->nutritionSpecialistMangersRepository->get($id);

        if (!$nutritionSpecialists) {
            return $this->badRequest(trans('nutritionSpecialist.notfound'));
        }

        $nutritionSpecialists = $this->repository->schedule($nutritionSpecialists);

        return $this->success([
            'nutritionSpecialists' => $nutritionSpecialists,
        ]);
    }

    /**
     * Method getHealthyDataUser
     *
     * @param $id $id
     *
     * @return void
     */
    public function getHealthyDataUser($id)
    {
        $optionStore = [
            'customer' => $id,
            'type' => 'products',
        ];
        $optionFood = [
            'customer' => $id,
            'type' => 'food',
        ];

        return $this->success([
            'customer' => $this->customersRepository->get((int) $id),
            'healthydata' => $this->healthyDatasRepository->getByModel('customerId', (int) $id),
            'orderStore' => $this->ordersRepository->list($optionStore),
            'orderFood' => $this->ordersRepository->list($optionFood),

        ]);
    }
}
