<?php

// Copyright


namespace App\Modules\Orders\Traits;

use App\Modules\Orders\Repositories\OrdersRepository;

class StatusColorSpecialist
{
    ///اللون : #14C864
    //حالة الطلب : تم الاستلام
    //اللون : #52656A
    //الحالة: في انتظار تأكيد المتجر
    //اللون: #00B2D8
    //تم قبول الطلب
    //اللون : #E9861C
    //قيد التوصيل
    // كوبون خصم : #00B2D8
    //عرض جديد #DC7612
    public static function statusColor($status): string
    {
        switch ($status) {
            case OrdersRepository::COMPLETED_STATUS:
                return '14C864';
            case OrdersRepository::PENDING_STATUS:
                return '00B2D8';
            case OrdersRepository::PROCESSING_STATUS:
            case 'coupon':
                return 'E9861C';
            case OrdersRepository::CANCELED_STATUS:
                return 'c8141e';
            case OrdersRepository::ADMIN_CANCELED_STATUS:
                return 'c8141e';
            case 'campaigning':
                return 'DC7612';
            default:
                return 'DC7613';
        }
    }

    public static function statusIcon($type): string
    {
        switch ($type) {
            case 'order':
                return asset('assets/icons/orders.png');
            case 'coupon':
                return asset('assets/icons/coupon.png');
            case 'campaigning':
                return asset('assets/icons/campaigning.png');
            default:
                return '';
        }
    }
}
