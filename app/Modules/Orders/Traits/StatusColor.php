<?php

// Copyright


namespace App\Modules\Orders\Traits;

use App\Modules\Orders\Repositories\OrdersRepository;

class StatusColor
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
                return '52656A';
            case OrdersRepository::PROCESSING_STATUS:
            case 'coupon':
                return '00B2D8';
            case OrdersRepository::ON_THE_WAY_STATUS:
                return 'E9861C';
            case OrdersRepository::CANCELED_STATUS:
                return 'c8141e';
            case OrdersRepository::ADMIN_CANCELED_STATUS:
                return 'c8141e';
            case OrdersRepository::RETURNED_STATUS:
                return 'c8141e';
            case OrdersRepository::REQUEST_RETURNING_STATUS:
                return '52656A';
            case OrdersRepository::REQUEST_RETURNING_ACCEPTED_STATUS:
                return '14C864';
            case OrdersRepository::REQUEST_RETURNING_REJECTED_STATUS:
                return 'c8141e';
            case OrdersRepository::ALTERNATIVE_PRODUCT:
                return '14C864';
            case OrdersRepository::WALLET_PRODUCT:
                return '14C864';
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
            case 'newCampaign':
                return asset('assets/icons/campaigning.png');
            default:
                return '';
        }
    }
}
