<?php

namespace App\Modules\Coupons\Repositories;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Modules\Customers\Models\Customer;
use App\Modules\Coupons\Models\Coupon as Model;
use App\Modules\Coupons\Filters\Coupon as Filter;
use App\Modules\Coupons\Resources\Coupon as Resource;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class CouponsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'coupons';

    /**
     * {@inheritDoc}
     */
    const MODEL = Model::class;

    /**
     * {@inheritDoc}
     */
    const RESOURCE = Resource::class;

    /**
     * Set the columns of the data that will be auto filled in the model
     *
     * @const array
     */
    const DATA = ['code', 'type', 'typeCoupon'];

    /**
     * Auto save uploads in this list
     * If it's an indexed array, in that case the request key will be as database column name
     * If it's associated array, the key will be request key and the value will be the database column name
     *
     * @const array
     */
    const UPLOADS = [];

    /**
     * Auto fill the following columns as arrays directly from the request
     * It will encoded and stored as `JSON` format,
     * it will be also auto decoded on any database retrieval either from `list` or `get` methods
     *
     * @const array
     */
    const ARRAYBLE_DATA = [];

    /**
     * Set columns list of integers values.
     *
     * @cont array
     */
    const INTEGER_DATA = ['maxUsage', 'rewardPoints'];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = ['value', 'minOrderValue'];

    /**
     * Set columns list of date values.
     *
     * @cont array
     */
    const DATE_DATA = ['startsAt', 'endsAt'];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = [
        'published',
    ];

    /**
     * Set the columns will be filled with single record of collection data
     * i.e [country => CountryModel::class]
     *
     * @const array
     */
    const DOCUMENT_DATA = [
        'customer' => Customer::class,
    ];

    /**
     * Set the columns will be filled with array of records.
     * i.e [tags => TagModel::class]
     *
     * @const array
     */
    const MULTI_DOCUMENTS_DATA = [];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array
     */
    const WHEN_AVAILABLE_DATA = ['code', 'type', 'maxUsage', 'value', 'minOrderValue', 'startsAt', 'endsAt', 'typeCoupon'];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'boolean' => ['published'],
        'like' => [
            'code',
            'typeCoupon',
        ],
        'int' => [
            'id',
            'group' => 'group.id',
        ],
    ];

    /**
     * Set all filter class you will use in this module
     *
     * @const array
     */
    const FILTERS = [
        Filter::class,
    ];

    /**
     * Determine wether to use pagination in the `list` method
     * if set null, it will depend on pagination configurations
     *
     * @const bool
     */
    const PAGINATE = true;

    /**
     * Number of items per page in pagination
     * If set to null, then it will taken from pagination configurations
     *
     * @const int|null
     */
    const ITEMS_PER_PAGE = 15;

    /**
     * Set any extra data or columns that need more customizations
     * Please note this method is triggered on create or update call
     *
     * @param mixed $model
     * @param \Illuminate\Http\Request $request
     * @return  void
     */
    protected function setData($model, $request)
    {
        
        if (!$model->type) {
            $model->type = 'fixed';
        }

        if (!$model->id) {
            $model->totalUses = 0;
        }

        if (!$model->id && !$model->rewardPoints) {
            $model->rewardPoints = 0;
        }
    }

    /**
     * Get and validate the given coupon code
     *
     * @param string $couponCode
     * @param Request $request
     * @return Model
     * @throws Exception
     */
    public function getValidCoupon(string $couponCode, Request $request)
    {
        $customer = user();

        // $coupon = $this->getByModel('code', $couponCode);
        $coupon = $this->couponsRepository->getQuery()->where('code', $couponCode)->first();

        $isCustomer = $customer && $customer->accountType() === 'customer';
        if ($isCustomer) {
            $customer->coupon()->clear();
        }

        if (!$coupon) {
            throw new Exception(trans('orders.coupons.notFoundCoupon'));
        }

        $type = request()->type;

        if ($coupon->typeCoupon && $coupon->typeCoupon != $type) {
            throw new Exception(trans('orders.coupons.notFoundCoupon'));
        }



        $now = Carbon::now();

        $startsAt = Carbon::parse($coupon->startsAt);
        // add one more day to the end date so it will end at the 12:00
        $endsAt = Carbon::parse($coupon->endsAt)->addDays(1);

        if (!$now->between($startsAt, $endsAt)) {
            // throw new Exception(trans('orders.coupons.invalidCoupon'));
            return null; //sprint 2 bugs fixed
        }

        if ($coupon['published'] == false) {
            // dd('sssss');
            throw new Exception(trans('orders.coupons.notFoundCoupon'));
        }

        // dd($coupon['published']);


        if ($coupon->maxUsage && $coupon->maxUsage <= $coupon->totalUses) {
            throw new Exception(trans('orders.coupons.invalidCoupon'));
        }
        // dd($coupon ,'aaaa');

        /*
        if ($coupon->group && (!$customer->group || !$coupon->group['id'] !== $customer->group['id'])) {
            throw new Exception(trans('orders.coupons.invalidCoupon'));
        }
        */

        $subTotal = $isCustomer ? $customer->getCart()->getSubTotal() : 0;

        // todo : refactor this
        //        if ($coupon->value > $subTotal) {
        //            throw new Exception(trans('orders.coupons.higherThanSubTotal'));
        //        }

        // skip validation for amount for visitors
        // dd($subTotal , $coupon->minOrderValue, $coupon->value);
        if ($subTotal && $coupon->minOrderValue && $subTotal < $coupon->minOrderValue) {
            throw new Exception(trans('orders.coupons.minValue', ['amount' => $coupon->minOrderValue]));
        }

        if ($customer && $customer->accountType() === 'customer') {
            $customer->coupon()->set($coupon);
        }

        // dd($subTotal);
        $discount = ($coupon->type == 'percentage') ? (($subTotal * $coupon->value) / 100) : $coupon->value;
        if ($subTotal >= $discount) {
            $coupon->couponDiscount = (float) $discount;
        } else {
            $coupon->couponDiscount = (float) $subTotal;
        }

        return $coupon;
    }

    /**
     * Increase the given coupon total uses
     *
     * @param Model $coupon
     * @return void
     */
    public function increaseTotalUses(Model $coupon)
    {
        $coupon->totalUses = ($coupon->totalUses ?? 0) + 1;

        $coupon->save();
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
    }

    /**
     * Method sendEmailForCoupon
     *
     * @return void
     */
    public function sendEmailForCoupon()
    {
        $coupons = $this->couponsRepository->getQuery()
            ->whereNotNull('customer')
            ->where('totalUses', '<', 1)
            ->where('endsAt', '>', Carbon::now())
            ->get();
        // dd($coupons);
        foreach ($coupons as $key => $coupon) {
            $storeNameMail = $this->settingsRepository->getSetting('deliveryMenEmails', 'storeNameMail');
            if ($coupon['customer']) {
                $email = $coupon['customer']['email'];
                $customer = $coupon['customer']['firstName'];
            } else {
                $email = 'test@gmail.com';
                $customer = 'test';
            }
            Mail::send([], [], function ($message) use ($customer, $storeNameMail, $email, $coupon) {
                $message->to($email)
                    ->subject('موعد انتهاء الكوبون')
                    // here comes what you want
                    ->setBody("
                    <p>
                        مرحبا   [{$customer}]
                    </p>
                    </br>
                    </br>
                    <hr>
                    </br>
                    <p>
                        لديك كوبون تخفيض {$coupon->code}
                    </p>
                    </br>
                    </br>
                    <p>
                        اوشكت علي انتهاء ولن يكون صالح الاستخدام في تاريخ {$coupon->endsAt}
                    </p>
                    </br>
                    </br>
                    <hr>
                    </br>
                    مع الشكر و التقدير
                    [{$storeNameMail}]
                ", 'text/html'); // assuming text/plain
            });
        }
        echo 'done';
    }
}
