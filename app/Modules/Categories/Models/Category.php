<?php

namespace App\Modules\Categories\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Category extends Model
{
    /**
     * TEST Comment 2
     * {@inheritDoc}
     */
    const SHARED_INFO = [
        'id', 'name', 'image', 'published', 'slug', 'type', 'color',
    ];
}
