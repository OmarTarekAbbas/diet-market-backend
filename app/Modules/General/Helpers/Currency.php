<?php

namespace App\Modules\General\Helpers;

class Currency
{
    /**
     * decimal point
     */
    const DECIMAL_POINT = '.';

    /**
     * thousand point
     */
    const THOUSAND_POINT = ',';

    /**
     * list of currencies
     *
     * @var array
     */
    public static $currencies = [];

    public function __construct()
    {
    }
   
    /**
     * retrieve currency from header
     *
     * @return mixed
     */
    public static function getCurrentCode()
    {
        return request()->header('CURRENCY', null) ?: 'SAR';
    }

    public static function getCurrencies()
    {
        return [
            'SAR' => [
                'name' => trans('currency.sar'),
                'code' => 'SAR',
                'symbol' => 'SAR',
                'value' => 1,
                'decimalValue' => 2,
            ],
            'KWD' => [
                'name' => trans('currency.kwd'),
                'code' => 'KWD',
                'symbol' => 'KWD',
                'value' => 3,
                'decimalValue' => 0,
            ],
        ];
    }

    /**
     * Get current currency
     *
     * @return string
     */
    public static function getCurrency(string $currencyCode = ''): array
    {
        $currencies = self::getCurrencies();

        $currencyCode = $currencyCode ?: self::getCurrentCode();

        return $currencies[$currencyCode] ?? [];
    }

    /**
     * Get current Symbol currency
     *
     * @return string
     */
    public static function getSymbol(string $currencyCode = ''): ?string
    {
        $currencyCode = $currencyCode ?: self::getCurrentCode();

        return self::getCurrency($currencyCode)['symbol'] ?? '';
    }

    /**
     * Get Decimal value currency
     *
     * @return int
     */
    public static function decimalValue(string $currencyCode = ''): ?string
    {
        $currencyCode = $currencyCode ?: self::getCurrentCode();

        return self::getCurrency($currencyCode)['decimalValue'] ?? 0;
    }

    /**
     * Get value currency
     *
     * @return float
     */
    public static function value(string $currencyCode = ''): ?string
    {
        $currencyCode = $currencyCode ?: self::getCurrentCode();

        return self::getCurrency($currencyCode)['value'] ?? 1.0;
    }

    /**
     * format number with currency
     *
     * @param float $number
     * @param string $currencyCode
     * @return string
     */
    public static function format($number, string $currencyCode = '', bool $format = true)
    {
        $decimalValue = self::decimalValue($currencyCode);
        
        $symbol = self::getSymbol($currencyCode);

        $value = self::value($currencyCode);

        $amount = $number * $value;

        $amount = round($amount, $decimalValue);

        if (!$format) {
            return (float) $amount;
        }

        $amount = number_format($amount, $decimalValue, self::DECIMAL_POINT, self::THOUSAND_POINT);

        return $amount . ' ' . $symbol;
    }

    /**
     * convert value from currency to other
     *
     * @param float $value
     * @param string $from
     * @param string $to
     * @return float
     */
    public static function convert(float $value, string $from, string $to): float
    {
        $from = self::value($from);

        $to = self::value($from);

        return (float) $value * ($to / $from);
    }

    /**
     * Check currency is exists
     *
     * @param string $currencyCode
     * @return bool
     */
    public function has(string $currencyCode): bool
    {
        return (bool) self::getCurrency($currencyCode);
    }
}
