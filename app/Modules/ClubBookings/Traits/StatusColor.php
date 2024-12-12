<?php

// Copyright


namespace App\Modules\ClubBookings\Traits;

use App\Modules\ClubBookings\Repositories\ClubBookingsRepository;

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
            case ClubBookingsRepository::COMPLETED_STATUS:
                return '95ca53';
            case ClubBookingsRepository::CANCELED_STATUS:
                return 'd7111a';
            case ClubBookingsRepository::ACCEPTED_STATUS:
                return '95ca53';
            case ClubBookingsRepository::REJECTED_STATUS:
                return 'd7111a';
            default:
                return 'cfd4c8';
        }
    }

    public static function statusName($status): string
    {
        switch ($status) {
            case ClubBookingsRepository::COMPLETED_STATUS:
                return trans('clubBooking.status.completed');
            case ClubBookingsRepository::CANCELED_STATUS:
                return trans('clubBooking.status.canceled');
            case ClubBookingsRepository::ACCEPTED_STATUS:
                return trans('clubBooking.status.accepted');
            case ClubBookingsRepository::REJECTED_STATUS:
                return trans('clubBooking.status.rejected');
            default:
                return trans('clubBooking.status.pending');
        }
    }
}
