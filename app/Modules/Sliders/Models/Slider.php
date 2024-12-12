<?php

namespace App\Modules\Sliders\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Slider extends Model
{
    /**
     * {@inheritdoc}
     */
    public const SHARED_INFO = ['id', 'banners', 'published'];
}
