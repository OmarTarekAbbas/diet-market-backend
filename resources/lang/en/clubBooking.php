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
    'taxesTextLang' => 'Taxes Value',
    'shippingFeesTextLang' => 'Shipping charges',
    'walletTextLang' => 'Wallet',
    'walletTextLangMinus' => 'wallet debt',
    'couponDiscountTextLang' => 'Coupon Discount',
    'finalPriceTextLang' => 'Final Price',
    'coupons' => [
        'notFoundCoupon' => 'This coupon is not available',
        'invalidCoupon' => 'This coupon is not valid',
        'higherThanSubTotal' => 'The coupon value is higher than the basket purchases',
        'minValue' => 'The minimum value to use this coupon is :amount SR',
        'dontHaveEnoughtPoints' => 'dont Have Enought Points',
        'maximumUsage' => 'maximum Usage',
    ],
    'paidOrderByWallet' => 'Order balance discount',
    'status' => [
        'pending' => 'Pending',
        'processing' => "Preparation in progress",
        'onTheWay' => "in the tracks",
        'deliveryOnTheWay' => "Delivery representative on the way",
        'deliveryReturned' => 'returned',
        'completed' => "Complete",
        'accepted' => "Accepted",
        'rejected' => "Rejected",
        'waitingToPickup' => 'Waiting for pickup from store',
        'requestReturning' => "Return request",
        'returned' => "bounce",
        'requestPartialReturn' => "Partial return request",
        'partiallyReturned' => "partial return",
        'canceled' => "canceled",
        'adminCanceled' => "Canceled by store",
    ],
    'ratedSuccess' => 'The request has been evaluated',
    'reorderClubs' => 'You cannot re-subscribe to an active account'

];
