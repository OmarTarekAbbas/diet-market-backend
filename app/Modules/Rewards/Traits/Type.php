<?php

// Copyright


namespace App\Modules\Rewards\Traits;

use App\Modules\Rewards\Repositories\RewardsRepository;

class Type
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
    public static function TransactionColor($status): string
    {
        switch ($status) {
            case RewardsRepository::DEPOSIT_TYPE:
                return '8CC63F';
            case RewardsRepository::WITHDRAW_TYPE:
                return 'DA2020';
            case RewardsRepository::EXCHANGE_TYPE:
                return 'DA9220';
            default:
                return '8CC63F';
        }
    }

    public static function TransactionIcon($status): string
    {
        switch ($status) {
            case RewardsRepository::DEPOSIT_TYPE:
                return asset('assets/icons/rewards/deposit.svg');
            case RewardsRepository::WITHDRAW_TYPE:
                return asset('assets/icons/rewards/withdraw.svg');
            case RewardsRepository::EXCHANGE_TYPE:
                return asset('assets/icons/rewards/exchange.svg');
            default:
                return asset('assets/icons/rewards/deposit.svg');
        }
    }
}
