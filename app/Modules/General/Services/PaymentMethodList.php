<?php

// Copyright
declare(strict_types=1);

namespace App\Modules\General\Services;

use App\Modules\Orders\Repositories\OrdersRepository;

final class PaymentMethodList
{
    public static function list()
    {
        $payment = collect([
            // [
            //     'code' => OrdersRepository::CASH_ON_DELIVERY,
            //     'name' => 'الدفع عند الاستلام',
            //     'icon' => asset('assets/payment/cash.png')
            // ],
            // [
            //     'code' => OrdersRepository::VISA_PAYMENT_METHOD,
            //     'name' => trans('orders.visa'),
            //     'icon' => asset('assets/payment/visa-masterCardNew.png')
            // ],
            // [
            //     'code' => OrdersRepository::MADA_PAYMENT_METHOD,
            //     'name' => trans('orders.mada'),
            //     'icon' => asset('assets/payment/madaNew.png')
            // ],
            // [
            //     'code' => OrdersRepository::APPLE_PAY_PAYMENT_METHOD,
            //     'name' => trans('orders.applePay'),
            //     'icon' => asset('assets/payment/applePayNew.png')
            // ],
        ]);
        // dd(request()->header('DEVICE-TYPE'));
        // dd(user()->walletBalance > user()->cart['finalPrice']);
        if (config('app.type') === 'site') {
            if (request()->url() == url('api/payment-methods')) {
                if (request()->header('DEVICE-TYPE') != "android" || request()->header('DEVICE-TYPE') != "web") {
                    $payment->push([
                        'code' => OrdersRepository::APPLE_PAY_PAYMENT_METHOD,
                        'name' => trans('orders.applePay'),
                        'icon' => asset('assets/payment/applePayNew.png'),
                        'fees' => 0,

                    ]);
                }
            } else {
                $payment->push([
                    'code' => OrdersRepository::APPLE_PAY_PAYMENT_METHOD,
                    'name' => trans('orders.applePay'),
                    'icon' => asset('assets/payment/applePayNew.png'),
                ]);
            }

            if (request()->type == 'food') {
                $settingFoodVisa = repo('settings')->getSetting('restaurant', 'onlineVisa');
                if ($settingFoodVisa == true) {
                    $payment->push([
                        'code' => OrdersRepository::VISA_PAYMENT_METHOD,
                        'name' => trans('orders.visa'),
                        'icon' => asset('assets/payment/visa-masterCardNew.png'),
                        'fees' => 0,

                    ]);
                    $payment->push([
                        'code' => OrdersRepository::MADA_PAYMENT_METHOD,
                        'name' => trans('orders.mada'),
                        'icon' => asset('assets/payment/madaNew.png'),
                        'fees' => 0,

                    ]);
                }

                if (user()->walletBalance >= user()->cartMeal['finalPrice']) {
                    if (request()->url() == url('api/payment-methods')) {
                        $payment->push([
                            'code' => OrdersRepository::WALLET_PAYMENT_METHOD,
                            'name' => trans('orders.wallet'),
                            'walletBalanceUser' => trans('orders.walletUes', ['value' => user()->walletBalance]),
                            'icon' => asset('assets/payment/walletNew.png'),
                            'fees' => 0,
                        ]);
                    } else {
                        $payment->push([
                            'code' => OrdersRepository::WALLET_PAYMENT_METHOD,
                            'name' => trans('orders.wallet'),
                            'icon' => asset('assets/payment/walletNew.png'),
                            'fees' => 0,
                        ]);
                    }
                }

                $settingFood = repo('settings')->getSetting('restaurant', 'cashOnDelivery');
                $settingCashOnDeliveryPrice = repo('settings')->getSetting('restaurant', 'cashOnDeliveryPrice');
                // dd($settingCashOnDeliveryPrice);
                $restaurant = repo('restaurants')->get((int) request()->restaurantId);
                // dd($restaurant['cashOnDeliveryValue'], $restaurant['cashOnDelivery']);
                if ($restaurant && $restaurant['cashOnDelivery'] == true) {
                    // dd($restaurant['cashOnDelivery']);
                    $payment->push([
                        'code' => OrdersRepository::CASH_ON_DELIVERY,
                        'name' => trans('orders.cashOnDelivery') . trans('orders.priceCach', ['value' => $restaurant['cashOnDeliveryValue']]),
                        'icon' => asset('assets/payment/cash.png'),
                        'fees' => $restaurant['cashOnDeliveryValue'],

                    ]);
                } elseif ($settingFood == true) {
                    $payment->push([
                        'code' => OrdersRepository::CASH_ON_DELIVERY,
                        'name' => trans('orders.cashOnDelivery') . trans('orders.priceCach', ['value' => $settingCashOnDeliveryPrice]),
                        'icon' => asset('assets/payment/cash.png'),
                        'fees' => $settingCashOnDeliveryPrice,

                    ]);
                }
            } elseif (request()->type == 'products') {
                // $settingRestaurant = repo('settings')->getSetting('restaurant', 'cashOnDelivery');
                // if ($settingRestaurant == true) {
                $settingStoreVisa = repo('settings')->getSetting('store', 'onlineVisa');
                if ($settingStoreVisa == true) {
                    $payment->push([
                        'code' => OrdersRepository::VISA_PAYMENT_METHOD,
                        'name' => trans('orders.visa'),
                        'icon' => asset('assets/payment/visa-masterCardNew.png'),
                        'fees' => 0,

                    ]);
                    $payment->push([
                        'code' => OrdersRepository::MADA_PAYMENT_METHOD,
                        'name' => trans('orders.mada'),
                        'icon' => asset('assets/payment/madaNew.png'),
                        'fees' => 0,

                    ]);
                }

                if (user()->walletBalance >= user()->cart['finalPrice']) {
                    if (request()->url() == url('api/payment-methods')) {
                        $payment->push([
                            'code' => OrdersRepository::WALLET_PAYMENT_METHOD,
                            'name' => trans('orders.wallet'),
                            'walletBalanceUser' => trans('orders.walletUes', ['value' => user()->walletBalance]),
                            'icon' => asset('assets/payment/walletNew.png'),
                            'fees' => 0,
                        ]);
                    } else {
                        $payment->push([
                            'code' => OrdersRepository::WALLET_PAYMENT_METHOD,
                            'name' => trans('orders.wallet'),
                            'icon' => asset('assets/payment/walletNew.png'),
                            'fees' => 0,
                        ]);
                    }
                }
            } elseif (request()->type == 'clubs') {
                $settingClubVisa = repo('settings')->getSetting('club', 'onlineVisa');
                if ($settingClubVisa == true) {
                    $payment->push([
                        'code' => OrdersRepository::VISA_PAYMENT_METHOD,
                        'name' => trans('orders.visa'),
                        'icon' => asset('assets/payment/visa-masterCardNew.png'),
                        'fees' => 0,
                    ]);
                    $payment->push([
                        'code' => OrdersRepository::MADA_PAYMENT_METHOD,
                        'name' => trans('orders.mada'),
                        'icon' => asset('assets/payment/madaNew.png'),
                        'fees' => 0,
                    ]);
                }

                $clubs = repo('packagesClubs')->get((int) request()->idpackagesClubs);
                if ($clubs) {
                    if (user()->walletBalance >= $clubs['finalPrice']) {
                        if (request()->url() == url('api/payment-methods')) {
                            $payment->push([
                                'code' => OrdersRepository::WALLET_PAYMENT_METHOD,
                                'name' => trans('orders.wallet'),
                                'walletBalanceUser' => trans('orders.walletUes', ['value' => user()->walletBalance]),
                                'icon' => asset('assets/payment/walletNew.png'),
                                'fees' => 0,
                            ]);
                        } else {
                            $payment->push([
                                'code' => OrdersRepository::WALLET_PAYMENT_METHOD,
                                'name' => trans('orders.wallet'),
                                'icon' => asset('assets/payment/walletNew.png'),
                                'fees' => 0,
                            ]);
                        }
                    }
                }

                $settingClubs = repo('settings')->getSetting('club', 'cashOnDelivery');
                $settingCashOnDeliveryPrice = repo('settings')->getSetting('club', 'cashOnDeliveryPrice');

                if ($settingClubs == true) {
                    $payment->push([
                        'code' => OrdersRepository::CASH_ON_DELIVERY,
                        'name' => trans('orders.cashOnClub') . trans('orders.priceCach', ['value' => $settingCashOnDeliveryPrice]),
                        'icon' => asset('assets/payment/cash.png'),
                        'fees' => $settingCashOnDeliveryPrice,

                    ]);
                }
            } elseif (request()->type == 'nutritionSpecialist') {
                $settingNutritionSpecialistVisa = repo('settings')->getSetting('nutritionSpecialist', 'onlineVisa');
                if ($settingNutritionSpecialistVisa == true) {
                    $payment->push([
                        'code' => OrdersRepository::VISA_PAYMENT_METHOD,
                        'name' => trans('orders.visa'),
                        'icon' => asset('assets/payment/visa-masterCardNew.png'),
                        'fees' => 0,
                    ]);
                    $payment->push([
                        'code' => OrdersRepository::MADA_PAYMENT_METHOD,
                        'name' => trans('orders.mada'),
                        'icon' => asset('assets/payment/madaNew.png'),
                        'fees' => 0,
                    ]);
                }
                $nutritionSpecialist = repo('nutritionSpecialistMangers')->get((int) request()->nutritionSpecialist);
                if ($nutritionSpecialist) {
                    if (user()->walletBalance >= $nutritionSpecialist['nutritionSpecialist']['finalPrice']) {
                        if (request()->url() == url('api/payment-methods')) {
                            $payment->push([
                                'code' => OrdersRepository::WALLET_PAYMENT_METHOD,
                                'name' => trans('orders.wallet'),
                                'walletBalanceUser' => trans('orders.walletUes', ['value' => user()->walletBalance]),
                                'icon' => asset('assets/payment/walletNew.png'),
                                'fees' => 0,
                            ]);
                        } else {
                            $payment->push([
                                'code' => OrdersRepository::WALLET_PAYMENT_METHOD,
                                'name' => trans('orders.wallet'),
                                'icon' => asset('assets/payment/walletNew.png'),
                                'fees' => 0,
                            ]);
                        }
                    }
                }
                if (request()->url() == url('api/orders/' . request()->route('id'))) {
                    $payment->push([
                        'code' => OrdersRepository::WALLET_PAYMENT_METHOD,
                        'name' => trans('orders.wallet'),
                        'icon' => asset('assets/payment/walletNew.png'),
                        'fees' => 0,
                    ]);
                }

                $settingNutritionSpecialist = repo('settings')->getSetting('nutritionSpecialist', 'cashOnDelivery');
                $settingCashOnDeliveryPrice = repo('settings')->getSetting('nutritionSpecialist', 'cashOnDeliveryPrice');
                if ($settingNutritionSpecialist == true) {
                    $payment->push([
                        'code' => OrdersRepository::CASH_ON_DELIVERY,
                        'name' => trans('orders.cashOnClinc') . trans('orders.priceCach', ['value' => $settingCashOnDeliveryPrice]),
                        'icon' => asset('assets/payment/cash.png'),
                        'fees' => $settingCashOnDeliveryPrice,
                    ]);
                }
            } else {
                $payment->push([
                    'code' => OrdersRepository::VISA_PAYMENT_METHOD,
                    'name' => trans('orders.visa'),
                    'icon' => asset('assets/payment/visa-masterCardNew.png'),
                ]);
                $payment->push([
                    'code' => OrdersRepository::MADA_PAYMENT_METHOD,
                    'name' => trans('orders.mada'),
                    'icon' => asset('assets/payment/madaNew.png'),
                ]);
                $payment->push([
                    'code' => OrdersRepository::CASH_ON_DELIVERY,
                    'name' => trans('orders.cashOnDelivery'),
                    'icon' => asset('assets/payment/cash.png'),
                ]);
            }

            return $payment;
        }
    }
}
