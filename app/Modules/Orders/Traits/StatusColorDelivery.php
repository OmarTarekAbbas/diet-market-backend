<?php

// Copyright


namespace App\Modules\Orders\Traits;

use App\Modules\Orders\Repositories\OrderDeliveryRepository;

class StatusColorDelivery
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
            case OrderDeliveryRepository::DELIVERY_PENDING_STATUS:
                return 'cef1fe';
            case OrderDeliveryRepository::ORDER_CANCELBYADMIN_STATUS:
                return 'fcdede';
            case OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS:
                return 'fbf1db';
            case OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS:
                return 'fbf1db';
            case OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS:
                return 'cefef3';
            case OrderDeliveryRepository::DELIVERY_NOTCOMPLETED_STATUS:
                return 'cefef3';
            case OrderDeliveryRepository::DELIVERY_REJECTED_STATUS:
                return 'fcdede';
            case 'newCampaignDelivery':
                return 'F6F6F6';
            default:
                return 'fbf1db';
        }
    }

    /**
     * Method iconColor
     *
     * @param $status $status
     *
     * @return string
     */
    public static function iconColor($status): string
    {
        // dd($status);
        switch ($status) {
            case OrderDeliveryRepository::DELIVERY_PENDING_STATUS:
                return '0d75f5';
            case OrderDeliveryRepository::ORDER_CANCELBYADMIN_STATUS:
                return 'ef3159';
            case OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS:
                return 'e69c0d';
            case OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS:
                return 'e69c0d';
            case OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS:
                return '0db38a';
            case OrderDeliveryRepository::DELIVERY_NOTCOMPLETED_STATUS:
                return '0db38a';
            case OrderDeliveryRepository::DELIVERY_REJECTED_STATUS:
                return 'ef3159';
            case 'newCampaignDelivery':
                return 'ffffff';
            default:
                return 'e69c0d';
        }
    }

    public static function statusIcon($type): string
    {
        switch ($type) {
            case 'order':
                return asset('assets/icons/orderDeliveryNew.png');
            case 'pullOrder':
                return asset('assets/icons/orderDeliveryNew.png');
            case 'cancelOrder':
                return asset('assets/icons/orderDeliveryNew.png');
            case 'acceptedOrder':
                return asset('assets/icons/orderDeliveryNew.png');
            case 'deliveryOnTheWayOrder':
                return asset('assets/icons/orderDeliveryNew.png');
            case 'completedOrder':
                return asset('assets/icons/orderDeliveryNew.png');
            case 'notCompletedOrder':
                return asset('assets/icons/orderDeliveryNew.png');
            case 'rejectedOrder':
                return asset('assets/icons/orderDeliveryNew.png');
            case 'coupon':
                return asset('assets/icons/coupon.png');
            case 'newCampaignDelivery':
                return asset('assets/icons/campaigning.png');
            default:
                return '';
        }
    }
}
