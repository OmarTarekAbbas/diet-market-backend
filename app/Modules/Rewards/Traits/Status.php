<?php

// Copyright


namespace App\Modules\Rewards\Traits;

use App\Modules\Rewards\Repositories\RewardsRepository;

class Status
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
    public static function StatusColor($status): string
    {
        switch ($status) {
            case RewardsRepository::ACTIVE_STATUS:
                return '8CC63F';
            case RewardsRepository::USED_STATUS:
                return '8CC63F';
            case RewardsRepository::EXPIRED_STATUS:
                return 'DA2020';
            default:
                return '8CC63F';
        }
    }
}
