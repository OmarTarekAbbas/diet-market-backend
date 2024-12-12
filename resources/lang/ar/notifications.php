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
        'newOrder' => 'تم إضافة طلبكم بنجاح',
        'newOrderContent' => 'سيتم مراجعة  و تجهيز طلبكم في أقرب وقت ممكن',
        'statusChanged' => 'تم تغيير حالة طلبكم الي :status',
        "newDeliveryRequestTitle" => 'طلبية جديدة',
        'newDeliveryRequest' => 'تم إسناد طلب جديد اليك :orderId',
        'title' => [
            'pending' => 'تم استلام طلبك',
            'processing' => 'طلبك قيد التجهيز',
            'onTheWay' => 'طلبك قيد التوصيل',
            'completed' => 'تم توصيل الطلب',
            'requestReturning' => 'تم تقديم طلب الإرجاع',
            'returned' => 'تم قبول طلب الإرجاع بنجاح',
            'requestPartialReturn' => 'تم قبول طلب الإرجاع بنجاح',
            'partiallyReturned' => 'تم قبول طلب الإرجاع بنجاح',
            'requestReturningAccepted' => 'تم قبول طلب الإرجاع بنجاح',
            'requestReturningRejected' => 'تم رفض طلب الإرجاع بنجاح',
            'canceled' => "طلب ملغي",
            'adminCanceled' => "طلب ملغي من قبل الادمن",
            'alternativeProduct' => "تم الموافقة على طلب الارجاع وارسال بديل",
            'walletProduct' => "تم الموافقة على طلب الارجاع وارجاع المبلغ",
        ],
        'content' => [
            'pending' => 'في انتظار تأكيد المتجر',
            'processing' => 'طلبك قيد اهتمامنا ',
            'onTheWay' => 'مندوبنا في السكة ',
            'completed' => 'تم توصيل طلبك بنجاح',
            'requestReturning' => 'تم تقديم طلب الإرجاع',
            'returned' => 'تم قبول طلب الإرجاع بنجاح',
            'requestPartialReturn' => 'تم تقديم طلب الإرجاع',
            'partiallyReturned' => 'تم قبول طلب الإرجاع بنجاح',
            'deliveryReturned' => 'تم قبول طلب الإرجاع بنجاح',
            'requestReturningAccepted' => 'تم قبول طلب الإرجاع بنجاح',
            'requestReturningRejected' => 'تم رفض طلب الإرجاع بنجاح',
            'canceled' => "طلب ملغي",
            'adminCanceled' => "طلب ملغي من قبل الادمن",
            'alternativeProduct' => "تم الموافقة على طلب الارجاع وارسال بديل",
            'walletProduct' => "تم الموافقة على طلب الارجاع وارجاع المبلغ",
        ],

        'titleFood' => [
            'pending' => 'تم استلام طلبك',
            'processing' => 'طلبك قيد التجهيز',
            'onTheWay' => 'طلبك قيد التوصيل',
            'completed' => 'تم توصيل الطلب',
            'requestReturning' => 'تم تقديم طلب الإرجاع',
            'returned' => 'تم قبول طلب الإرجاع بنجاح',
            'requestPartialReturn' => 'تم قبول طلب الإرجاع بنجاح',
            'partiallyReturned' => 'تم قبول طلب الإرجاع بنجاح',
            'requestReturningAccepted' => 'تم قبول طلب الإرجاع بنجاح',
            'requestReturningRejected' => 'تم رفض طلب الإرجاع بنجاح',
            'canceled' => "طلب ملغي",
            'adminCanceled' => "طلب ملغي من قبل الادمن",
        ],
        'contentFood' => [
            'pending' => 'في انتظار تأكيد المطعم',
            'processing' => 'طلبك قيد اهتمامنا ',
            'onTheWay' => 'مندوبنا في السكة ',
            'completed' => 'تم توصيل طلبك بنجاح',
            'requestReturning' => 'تم تقديم طلب الإرجاع',
            'returned' => 'تم قبول طلب الإرجاع بنجاح',
            'requestPartialReturn' => 'تم تقديم طلب الإرجاع',
            'partiallyReturned' => 'تم قبول طلب الإرجاع بنجاح',
            'deliveryReturned' => 'تم قبول طلب الإرجاع بنجاح',
            'requestReturningAccepted' => 'تم قبول طلب الإرجاع بنجاح',
            'requestReturningRejected' => 'تم رفض طلب الإرجاع بنجاح',
            'canceled' => "طلب ملغي",
            'adminCanceled' => "طلب ملغي من قبل الادمن",
        ],
        'titleClub' => [
            'pending' => 'في انتظار تأكيد النادي',
            'completed' => "تم تأكيد الاشتراك",
            'canceled' => "تم رفض الاشتراك",
            'unsubscribe' => "تم إلغاء الاشتراك",
            'adminCanceled' => "اشتراك ملغي",
        ],
        'contentClub' => [
            'pending' => 'في انتظار تأكيد النادي',
            'completed' => "تم تأكيد الاشتراك",
            'canceled' => "تم رفض الاشتراك",
            'unsubscribe' => "تم إلغاء الاشتراك",
            'adminCanceled' => "اشتراك ملغي",
        ],

        'titleNutritionSpecialist' => [
            'pending' => 'في انتظار تأكيد الأخصائي',
            'processing' => "تم قبول الحجز",
            'completed' => "تم الانتهاء",
            'canceled' => "حجز ملغي",
            'adminCanceled' => "تم رفض الحجز",
        ],


        'completedOrder' => [
            'delivery' => 'برجاء توصيل الوجبة الي العميل',
        ],

    ],

    'newCustomerGroup' => [
        'title' => [
            'specialDiscount' => 'خصم خاص',
            'freeShipping' => 'الشحن مجانا',
            'freeExpressShipping' => 'الشحن السريع',
        ],
        'content' => [
            'specialDiscount' => 'خصم خاص',
            'freeShipping' => 'الشحن مجانا',
            'freeExpressShipping' => 'الشحن السريع'
        ],
    ],

    'wallet' => [
        'withdrawTitle' => 'سحب من المحفظة',
        'withdrawContent' => 'سحب من المحفظة',
        'depositTitle' => 'ايداع في المحفظة',
        'depositContent' => 'ايداع في المحفظة',
        'orderReturnedAmount' => 'يتم ارجاع المبلغ :value ر.س لحسابك طبقأ لطلب الارجاع',
        'order' => 'تم اضافة رصيد :amount ر.س الي محفظتك بسبب وجود طلب جديد برقم :value',
    ],

    'rewards' => [
        'withdrawTitle' => 'سحب من النقاط',
        'withdrawContent' => 'سحب من النقاط',
        'depositTitle' => 'ايداع في النقاط',
        'depositContent' => 'ايداع في النقاط',
    ],
];
