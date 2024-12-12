<?php

namespace App\Modules\Clubs\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;
use App\Modules\Orders\Repositories\OrdersRepository;

class ClubReviewsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'clubReviews';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'club' => (int) $request->club,
            'orderId' => (int) $request->orderId,
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
            'records' => $this->repository->list($options),
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
        $validator = $this->scan($request);

        if ($validator->passes()) {
            $order = $this->ordersRepository->getModel($request->orderId);

            $club = $this->clubsRepository->getModel($request->clubId);
            // dd($club);ProductsRepository
            if (!$club) {
                return $this->badRequest(trans('products.notFound'));
            }

            if (!$order) {
                return $this->badRequest(trans('auth.invalidData'));
            }
            // dd($order->status);
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

            if ($this->repository->isRating($request->orderId, $request->clubId)) {
                return $this->badRequest(trans('errors.alreadyRatedClub'));
            }

            $this->repository->create($request);

            $order = $this->ordersRepository->wrap($order->fresh());

            return $this->success([
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
            "clubId" => 'required',
            "rate" => 'required',
        ]);
    }
}
