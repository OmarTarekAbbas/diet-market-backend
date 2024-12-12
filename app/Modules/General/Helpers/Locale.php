<?php

namespace App\Modules\General\Helpers;

use HZ\Illuminate\Mongez\Helpers\Mongez;

class Locale
{
    /**
     * Get name
     *
     * @return mixed
     */
    public static function getColumn($column)
    {
        $value = $column;

        if (empty($column)) {
            return '';
        }

        if (is_string($value) || !Mongez::requestHasLocaleCode()) {
            return $value;
        }

        $localeCode = Mongez::getRequestLocaleCode();

        // get the localization mode
        // it cn be an object or an array of objects
        $localizationMode = config('mognez.localizationMode', 'array');

        // the OR in the following if conditions is used as a fallback for the data that is
        // not matching the current localization mode
        // for example, if the data is stored as object and the localization mode is an array
        // in that case it will be rendered as an array

        if ($localizationMode === 'array' && isset($value[0]) || isset($value[0])) {
            foreach ($value as $localizedValue) {
                if ($localizedValue['localeCode'] === $localeCode) {
                    return (string) $localizedValue['text'];
                }
            }
        } elseif ($localizationMode === 'object' && isset($value[$localeCode]) || isset($value[$localeCode])) {
            return (string) $value[$localeCode];
        }

        return $value;
    }
}
