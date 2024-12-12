<?php

namespace App\Modules\Orders\Filters;

use HZ\Illuminate\Mongez\Helpers\Filters\MongoDB\Filter;

class OrderItem extends Filter
{
    /**
     * List with all filter.
     *
     * Order => functionName
     * @const array
     */
    const FILTER_MAP = [];
}
