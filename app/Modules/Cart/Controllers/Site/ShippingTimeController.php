<?php

// Copyright
declare(strict_types=1);


namespace App\Modules\Cart\Controllers\Site;

use Carbon\Carbon;
use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Helpers\Mongez;
use HZ\Illuminate\Mongez\Managers\ApiController;

class ShippingTimeController extends ApiController
{
    /**
     * generate shipping time for 4 weeks
     *
     * @par$am Request $request
     * @return \Illuminate\Http\Response|string
     */
    public function shippingTime(Request $request)
    {
        $lang = 'en_US';

        if (app()->getLocale() == 'ar') {
            $lang = 'ar_SA';
        }

        $now = Carbon::now()->locale($lang);

        $days = $this->getDays($now, 4);

        return $this->success([
            'records' => $days,
        ]);
    }

    /**
     * generate shipping time for 4 weeks
     *
     * @par$am Request $request
     * @return \Illuminate\Http\Response|string
     */
    public function shippingTimeWeeks(Request $request)
    {
        $lang = 'en_US';

        if (app()->getLocale() == 'ar') {
            $lang = 'ar_SA';
        }

        $now = Carbon::now()->locale($lang);

        $weeks = [
            'firstWeek' => [],
            'secondWeek' => [],
            'thirdWeek' => [],
            'fourthWeek' => [],
        ];

        foreach ($weeks as $key => $week) {
            $weeks[$key] = [
                'weekName' => $key,
                'weekText' => trans("weeks.{$key}"),
            ];

            $weeks[$key]['times'] = $this->getDays($now, 7);
        }

        return $this->success([
            'records' => $weeks,
        ]);
    }

    public function getDays(Carbon $now, int $daysCount): array
    {
        $days = [];

        $todayText = trans('days.today');
        $tomorrowText = trans('days.tomorrow');

        for ($dayNumber = 1; $dayNumber <= $daysCount; $dayNumber++) {
            $day = $Hours = $now;

            if (!$day->isToday()) {
                $day = $day->setTime(0, 0, 0);
                $Hours = $Hours->setTime(0, 0, 0);
            }

            $times = $this->getTimes();

            $workingHours = [];

            $hour = $Hours->format('H');

            // hours in day from 8 $am to 11 $pm
            foreach ($times as $time) {
                $hour = $Hours->format('H');

                if (((int) $hour + 4) > $time['from']) {
                    continue;
                }

                $time['hourText'] = $this->convertNumber($time['hourText']);
                $time['hour'] = $this->convertNumber($time['hourText']);

                $workingHours[] = $time;

                $hour = $Hours->addHour();
            }

            $dayTime = '';

            if ($day->isToday()) {
                $dayTime = ' - (' . $todayText . ')';
            } elseif ($day->isTomorrow()) {
                $dayTime = ' - (' . $tomorrowText . ')';
            }

            if (count($workingHours)) {
                $days[] = [
                    'dayName' => $day->shortDayName . $dayTime, // for display only
                    'date' => $day->format('Y-m-d'), // will be returned
                    'dateText' => $day->format('d/m'), // for display only
                    'workingHours' => $workingHours,
                ];
            }

            $now = $day->setTime(0, 0, 0)->addDay();
        }

        return $days;
    }

    /**
     * times in day
     *
     * @return array[]
     */
    public function getTimes(): array
    {
        // $localeCode = Mongez::getRequestLocaleCode() ?: 'en';

        // $am = $localeCode === 'en' ? 'AM' : 'ص';
        // $pm = $localeCode === 'en' ? 'PM' : 'م';

        $am = trans('days.am');
        $pm = trans('days.pm');

        return [
            [
                'from' => 8,
                'to' => 10,
                "hourText" => "8 {$am} - 10 {$am}",
                "hour" => "8 {$am} - 10 {$am}",
            ],
            [
                'from' => 10,
                'to' => 12,
                "hourText" => "10 {$am} - 12 {$pm}",
                "hour" => "10 {$am} - 12 {$pm}",
            ],
            [
                'from' => 12,
                'to' => 14,
                "hourText" => "12 {$pm} - 2 {$pm}",
                "hour" => "12 {$pm} - 2 {$pm}",
            ],
            [
                'from' => 16,
                'to' => 18,
                "hourText" => "4 {$pm} - 6 {$pm}",
                "hour" => "4 {$pm} - 6 {$pm}",
            ],
            [
                'from' => 18,
                'to' => 20,
                "hourText" => "6 {$pm} - 8 {$pm}",
                "hour" => "6 {$pm} - 8 {$pm}",
            ],
            [
                'from' => 20,
                'to' => 22,
                "hourText" => "8 {$pm} - 10 {$pm}",
                "hour" => "8 {$pm} - 10 {$pm}",
            ],
        ];
    }

    public static function convertNumber($string)
    {
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        $english = range(0, 9);

        if (app()->getLocale() == 'ar') {
            $englishNumbersOnly = str_replace($english, $arabic, $string);
        } else {
            $englishNumbersOnly = str_replace($arabic, $english, $string);
        }

        return $englishNumbersOnly;
    }
}
