<?php

namespace App\Modules\NutritionSpecialistMangers\Controllers\Site;

use Illuminate\Http\Request;
use App\Modules\General\Helpers\Visitor;
use HZ\Illuminate\Mongez\Managers\ApiController;

class NutritionSpecialistMangersController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'nutritionSpecialistMangers';

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
            'nutritionSpecialistPublished' => true,

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
            'paginationInfo' => $this->repository->getPaginateInfo(),
            'returnOrderStatus' => $this->settingsRepository->getSetting('ReturnedOrder', 'returnSystemStatus'),
            'isHealthyData' => ($healthyData) ? true : false,

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
            'sortRating' => $request->sortRating,
            'highestRating' => $request->highestRating,
            'lowestRating' => $request->lowestRating,
            'latest' => $request->latest,
            'oldest' => $request->oldest,
            'sort' => $request->sort,
            'published' => true,
        ];

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'record' => $nutritionSpecialist,
            'reviews' => $this->nutritionSpecialistReviewsRepository->list($options),
        ]);
    }
}
