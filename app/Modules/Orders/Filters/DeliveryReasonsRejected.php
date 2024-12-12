<?php

namespace App\Modules\Orders\Filters;

use HZ\Illuminate\Mongez\Helpers\Filters\MongoDB\Filter;

class DeliveryReasonsRejected extends Filter
{
    /**
     * List with all filter.
     *
     * ReturningReason => functionName
     * @const array
     */
    const FILTER_MAP = [];
}
