<?php

namespace App\Modules\Coupons\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class CouponsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'coupons';

    /**
     * {@inheritDoc}
     */
    public function verify($coupon, Request $request)
    {
        try {
            if (!user()->cart['items']) {
                return $this->badRequest(trans('cart.empty'));
            }

            return $this->success([
                'coupon' => $this->repository->wrap(
                    $this->repository->getValidCoupon($coupon, $request)
                ),
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return (env('APP_ENV') == 'dev') ? $this->badRequest($th->getMessage() . $th->getTraceAsString()) : $this->badRequest($th->getMessage());
        }
    }

    /**
     * Method sendEmailForCoupon
     *
     * @return void
     */
    public function sendEmailForCoupon()
    {
        $this->repository->sendEmailForCoupon();
    }
}
