<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pagination Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the paginator library to build
    | the simple pagination links. You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */
    'price' => ':value SR',
    'subTotalTextLang' => 'Products cost',
    'subTotalTextLangProduct' => 'Products cost',
    'subTotalTextLangMeals' => 'Melas cost',
    'subTotalTextLangClubs' => 'Subscribe cost',
    'subTotalTextLangNutritionSpecialist' => 'NutritionSpecialist cost',
    'reviewSchedule' => 'Service cost',
    'reviewScheduleMun' => 'Balance deduction',
    'taxesTextLang' => 'Taxes Value',
    'shippingFeesTextLang' => 'Shipping charges',
    'shippingFeesTextLangMeals' => 'Shipping charges',
    'walletTextLang' => 'Wallet',
    'walletTextLangMinus' => 'wallet debt',
    'couponDiscountTextLang' => 'Coupon Discount',
    'couponDiscountFoodTextLang' => 'Coupon Discount',
    'finalPriceTextLang' => 'Final Price',
    'finalPriceTextLangMeals' => 'Final Price',
    'cashOnDeliveryPrice' => 'cashOnDeliveryPrice',
    'alternativeProduct' => 'send alternative',
    'walletProduct' => 'Refund',
    'pendingReplace' => 'Pending',

    'coupons' => [
        'notFoundCoupon' => 'This coupon is not available',
        'invalidCoupon' => 'This coupon is not valid',
        'higherThanSubTotal' => 'The coupon value is higher than the basket purchases',
        'minValue' => 'The minimum value to use this coupon is :amount SR',
        'dontHaveEnoughtPoints' => 'dont Have Enought Points',
        'maximumUsage' => 'maximum Usage',
    ],
    'paidOrderByWallet' => 'Order balance discount',
    'newOrder' => 'newOrder',
    'status' => [
        'pending' => 'Pending',
        'processing' => "Preparation in progress",
        'onTheWay' => "in the tracks",
        'deliveryOnTheWay' => "Delivery representative on the way",
        'deliveryReturned' => 'returned',
        'completed' => "complete",
        'waitingToPickup' => 'Waiting for pickup from store',
        'requestReturning' => "Return request",
        'returned' => "bounce",
        'requestPartialReturn' => "Partial return request",
        'partiallyReturned' => "partial return",
        'canceled' => "canceled",
        'adminCanceled' => "Canceled by Admin",
        'requestReturningAccepted' => "request Returning Accepted",
        'requestReturningRejected' => "request Returning Rejected",
        'alternativeProduct' => "The return request has been approved and a replacement has been sent",
        'walletProduct' => "The return request has been approved and the amount will be refunded",

    ],
    'statusMeals' => [
        'pending' => 'Pending',
        'processing' => "Preparation in progress",
        'onTheWay' => "in the tracks",
        'deliveryOnTheWay' => "Delivery representative on the way",
        'deliveryReturned' => 'returned',
        'completed' => "complete",
        'waitingToPickup' => 'Waiting for pickup from store',
        'requestReturning' => "Return request",
        'returned' => "bounce",
        'requestPartialReturn' => "Partial return request",
        'partiallyReturned' => "partial return",
        'canceled' => "canceled",
        'adminCanceled' => "Canceled by Admin",
        'requestReturningAccepted' => "request Returning Accepted",
        'requestReturningRejected' => "request Returning Rejected",
    ],
    'statusClubs' => [
        'pending' => 'Waiting for club confirmation',
        'completed' => "Subscription confirmed",
        'canceled' => "Subscription denied",
        'unsubscribe' => "canceled subscription",
        'adminCanceled' => "Request canceled by admin",
    ],
    'statusNutritionSpecialist' => [
        'pending' => 'Waiting for specialist confirmation',
        'processing' => "Booking accepted",
        'completed' => "Been completed",
        'canceled' => "canceled reservation",
        'adminCanceled' => "canceled reservation",
    ],
    'ratedSuccess' => 'The request has been evaluated',
    'reorderClubs' => 'You cannot re-subscribe to an active account',
    'visa' => 'By credit card',
    'mada' => 'By Mada',
    'applePay' =>  'By Apple Pay',
    'wallet' =>  'By Wallet',
    'priceCach' => '( :value SR )',
    'walletUes' =>  'You have ( :value SR ) in your wallet',
    'cashOnDelivery' =>  'By cashOnDelivery',
    'cashOnClub' =>  'Pay at the club',
    'cashOnClinc' =>  'Pay at the clinic',
    'emptyCart' =>  'The order cannot be completed at the moment due to the restaurant becoming unavailable',


];
