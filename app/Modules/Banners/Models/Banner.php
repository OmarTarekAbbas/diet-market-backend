<?php

namespace App\Modules\Banners\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Banner extends Model
{
    /**
     * {@inheritdoc}
     */
    public const SHARED_INFO = ['id', 'type', 'extra', 'typeId', 'image', 'published'];
}
