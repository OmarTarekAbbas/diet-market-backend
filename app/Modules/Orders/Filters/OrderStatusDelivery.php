<?php

namespace App\Modules\Orders\Filters;

use HZ\Illuminate\Mongez\Helpers\Filters\MongoDB\Filter;

class OrderStatusDelivery extends Filter
{
    /**
     * List with all filter.
     *
     * Order => functionName
     * @const array
     */
    const FILTER_MAP = [];
}
