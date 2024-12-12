<?php

namespace App\Modules\Favorites\Filters;

use HZ\Illuminate\Mongez\Helpers\Filters\MongoDB\Filter;

class Favorite extends Filter
{
    /**
     * List with all filter.
     *
     * Favorite => functionName
     * @const array
     */
    const FILTER_MAP = [];
}
