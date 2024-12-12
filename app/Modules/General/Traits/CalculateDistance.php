<?php

// Copyright


namespace App\Modules\General\Traits;

use Illuminate\Support\Arr;

trait CalculateDistance
{
    /**
     * @param $lat1
     * @param $lon1
     * @param $lat2
     * @param $lon2
     * @param string $unit
     * @return float|int
     */
    public static function distance($lat1, $lon1, $lat2, $lon2, $unit = 'K')
    {
        // dd($lat1, $lon1, $lat2, $lon2, $unit);
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                return round(($miles * 1.609344), 2);
            } elseif ($unit == "N") {
                return round(($miles * 0.8684), 2);
            } else {
                return round($miles, 2);
            }
        }
    }

    /**
     * @param $request
     * @param $location
     * @param bool $twoLocation
     * @return float|int
     */
    public static function getDistance($request, $location, $twoLocation = false)
    {
        if ($twoLocation) {
            $userLocation = $request['coordinates'];
        } elseif ($location) {
            $userLocation = [
                $request['coordinates'][0],
                $request['coordinates'][1],
            ];
        } else {
            return 0;
        }

        if (Arr::has($location, 'coordinates')) {
            $location = $location['coordinates'];
        } else {
            $location = [$location['lat'], $location['lng']];
        }

        return round(self::distance($location[0], $location[1], $userLocation[0], $userLocation[1]), 2);
    }
}
