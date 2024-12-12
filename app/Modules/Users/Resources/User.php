<?php

namespace App\Modules\Users\Resources;

use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class User extends JsonResourceManager
{
    /**
     * {@inheritDoc}
     */
    const DATA = ['id', 'name', 'email','type'];

    /**
     * {@inheritDoc}
     */
    const ASSETS = [];

    /**
     * {@inheritDoc}
     */
    const WHEN_AVAILABLE = ['type'];

    /**
     * {@inheritDoc}
     */
    const RESOURCES = [
        'group' => UsersGroup::class,
    ];

    /**
     * {@inheritDoc}
     */
    const COLLECTABLE = [];
}
