<?php

namespace App\Modules\Rewards\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class RewardsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'rewards';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $user = user();

        $options = [
            'customer' => $user->id,
            'page' => $request->page ?? 1,
        ];

        $records = $this->repository->list($options);

        $minExchangePoints = $this->settingsRepository->getSetting('reward', 'minExchangePoints') ?? 20;

        $data = $this->repository->getUserPoints();

        return $this->success([
            'userPoints' => $data['userPoints'],
            'userPointsPrice' => $data['userPointsPrice'],
            'availablePoints' => $data['availablePoints'],
            'availablePointsPrice' => $data['availablePointsPrice'],
            'canExchange' => ($data['userPoints'] > 0 && $data['userPoints'] >= $minExchangePoints) ? true : false,
            'records' => $records,
            'paginationInfo' => $this->repository->getPaginateInfo(),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function genrateRewardCoupon(Request $request)
    {
        $user = user();

        $minExchangePoints = $this->settingsRepository->getSetting('reward', 'minExchangePoints') ?? 20;

        if ($user->rewardPoint >= $minExchangePoints) {
            $maxExchangePoints = $this->settingsRepository->getSetting('reward', 'maxExchangePoints') ?? 1000;

            $points = $user->rewardPoint;

            if ($points > $maxExchangePoints) {
                $points = $maxExchangePoints;
            }

            $coupon = $this->repository->genrateRewardCoupon($points, $user->id);

            $data = $this->repository->getUserPoints();

            return $this->success([
                'record' => $this->couponsRepository->wrap($coupon),
                'userPoints' => $data['userPoints'],
                'userPointsPrice' => $data['userPointsPrice'],
                'availablePoints' => $data['availablePoints'],
                'availablePointsPrice' => $data['availablePointsPrice'],
                'canExchange' => ($data['userPoints'] > 0 && $data['userPoints'] >= $minExchangePoints) ? true : false,

            ]);
        } else {
            return $this->badRequest(trans('rewards.notEnoughPoints'));
        }
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
}
