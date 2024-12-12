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
    'order' => [
        'newOrder' => 'Your request has been successfully added',
        'newOrderContent' => 'Your request will be reviewed and processed as soon as possible',
        'statusChanged' => 'The status of your request has been changed to :status',
        "newDeliveryRequestTitle" => 'new order',
        'newDeliveryRequest' => 'You have been assigned a new request :orderId',
        'title' => [
            'pending' => 'Your request has been received',
            'processing' => 'Your order is being processed',
            'onTheWay' => 'Your order is being delivered',
            'completed' => 'The order has been delivered',
            'requestReturning' => 'Return request has been submitted',
            'returned' => 'Return request has been successfully accepted',
            'requestPartialReturn' => 'Return request has been successfully accepted',
            'partiallyReturned' => 'Return request has been successfully accepted',
            'requestReturningAccepted' => 'Return request has been successfully accepted',
            'requestReturningRejected' => 'Return request has been successfully Rejected',
            'canceled' => "canceled",
            'adminCanceled' => "Canceled by Admin",
            'alternativeProduct' => "The return request has been approved and a replacement has been sent",
            'walletProduct' => "The return request has been approved and the amount will be refunded",
        ],
        'content' => [
            'pending' => 'Your request has been received',
            'processing' => 'Your order is being processed',
            'onTheWay' => 'Your order is being delivered',
            'completed' => 'The order has been delivered',
            'requestReturning' => 'Return request has been submitted',
            'returned' => 'Return request has been successfully accepted',
            'requestPartialReturn' => 'Return request has been successfully accepted',
            'partiallyReturned' => 'Return request has been successfully accepted',
            'deliveryReturned' => 'delivery Returned',
            'requestReturningAccepted' => 'Return request has been successfully accepted',
            'requestReturningRejected' => 'Return request has been successfully Rejected',
            'canceled' => "canceled",
            'adminCanceled' => "Canceled by Admin",
            'alternativeProduct' => "The return request has been approved and a replacement has been sent",
            'walletProduct' => "The return request has been approved and the amount will be refunded",
        ],
        'titleFood' => [
            'pending' => 'Your request has been received',
            'processing' => 'Your order is being processed',
            'onTheWay' => 'Your order is being delivered',
            'completed' => 'The order has been delivered',
            'requestReturning' => 'Return request has been submitted',
            'returned' => 'Return request has been successfully accepted',
            'requestPartialReturn' => 'Return request has been successfully accepted',
            'partiallyReturned' => 'Return request has been successfully accepted',
            'requestReturningAccepted' => 'Return request has been successfully accepted',
            'requestReturningRejected' => 'Return request has been successfully Rejected',
            'canceled' => "canceled",
            'adminCanceled' => "Canceled by Admin",

        ],
        'contentFood' => [
            'pending' => 'Your request has been received',
            'processing' => 'Your order is being processed',
            'onTheWay' => 'Your order is being delivered',
            'completed' => 'The order has been delivered',
            'requestReturning' => 'Return request has been submitted',
            'returned' => 'Return request has been successfully accepted',
            'requestPartialReturn' => 'Return request has been successfully accepted',
            'partiallyReturned' => 'Return request has been successfully accepted',
            'deliveryReturned' => 'delivery Returned',
            'requestReturningAccepted' => 'Return request has been successfully accepted',
            'requestReturningRejected' => 'Return request has been successfully Rejected',
            'canceled' => "canceled",
            'adminCanceled' => "Canceled by Admin",
        ],
        'titleClub' => [
            'pending' => 'Waiting for club confirmation',
            'completed' => "Subscription confirmed",
            'canceled' => "Subscription denied",
            'unsubscribe' => "canceled subscription",
            'adminCanceled' => "Request canceled by admin",
        ],
        'contentClub' => [
            'pending' => 'Waiting for club confirmation',
            'completed' => "Subscription confirmed",
            'canceled' => "Subscription denied",
            'unsubscribe' => "canceled subscription",
            'adminCanceled' => "Request canceled by admin",
        ],

        'titleNutritionSpecialist' => [
            'pending' => 'Waiting for specialist confirmation',
            'processing' => "Booking accepted",
            'completed' => "Been completed",
            'canceled' => "canceled reservation",
            'adminCanceled' => "canceled reservation",
        ],
        'contentClub' => [
            'pending' => 'Waiting for specialist confirmation',
            'processing' => "Booking accepted",
            'completed' => "Been completed",
            'canceled' => "canceled reservation",
            'adminCanceled' => "canceled reservation",
        ],
        'completedOrder' => [
            'delivery' => 'Please deliver the meal to the customer',
        ],
    ],

    'newCustomerGroup' => [
        'title' => [
            'specialDiscount' => 'specialDiscount',
            'freeShipping' => 'freeShipping',
            'freeExpressShipping' => 'freeExpressShipping',
        ],
        'content' => [
            'specialDiscount' => 'specialDiscount',
            'freeShipping' => 'freeShipping',
            'freeExpressShipping' => 'freeExpressShipping',
        ],
    ],
    'wallet' => [
        'withdrawTitle' => 'withdraw Wallet',
        'withdrawContent' => 'withdraw Wallet',
        'depositTitle' => 'deposit Wallet',
        'depositContent' => 'deposit Wallet',
        'orderReturnedAmount' => 'The amount will be value: SR the value of SAR to your account according to the return request',
        'order' => 'تم اضافة رصيد :amount ر.س الي محفظتك بسبب وجود طلب جديد برقم :value',

    ],

    'rewards' => [
        'withdrawTitle' => 'withdraw Rewards',
        'withdrawContent' => 'withdraw Rewards',
        'depositTitle' => 'deposit Rewards',
        'depositContent' => 'deposit Rewards',
    ],
];
