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
    'price' => ':value ر.س',
    'subTotalTextLang' => 'تكلفة المنتجات',
    'subTotalTextLangProduct' => 'تكلفة المنتجات',
    'subTotalTextLangMeals' => 'مجموع الأصناف',
    'subTotalTextLangClubs' => 'تكلفة الاشتراك',
    'taxesTextLang' => 'قيمة الضريبة',
    'shippingFeesTextLang' => 'تكلفة الشحن',
    'walletTextLang' => 'حساب دائن',
    'walletTextLangMinus' => 'ديون المحفظة',

    'couponDiscountTextLang' => 'خصم الكوبون',
    'finalPriceTextLang' => 'إجمالي الفاتورة',
    'coupons' => [
        'notFoundCoupon' => 'هذا الكوبون غير موجود',
        'invalidCoupon' => 'هذا الكوبون غير صالح',
        'higherThanSubTotal' => 'قيمة الكوبون أعلى من مشتريات السلة',
        'minValue' => 'أقل قيمة لاستخدام هذا الكوبون هو :amount رس',
        'dontHaveEnoughtPoints' => 'لا تملك النقاط المطلوبة',
        'maximumUsage' => 'الحد الأقصى',
    ],
    'paidOrderByWallet' => 'خصم رصيد طلب',
    'status' => [
        'pending' => 'في انتظار تأكيد النادي',
        'processing' => "تم قبول الطلب وجاري التجهيز",
        'completed' => "تم الانتهاء",
        'rejected' => "تم رفض الحجز",
        'accepted' => "تم تأكيد الحجز",
        'canceled' => "حجز ملغي",
        'adminCanceled' => "ملغي من قبل النادي",
    ],
    'ratedSuccess' => 'تم تقيم الطلب',
    'reorderClubs' => 'لا يمكنك اعادة الاشتراك لشتراك فعال'
];
