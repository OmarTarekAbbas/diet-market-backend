<?php

// Copyright
declare(strict_types=1);

namespace App\Modules\Newsletters\Services\Gateways;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;
use App\Modules\Newsletters\Services\Contracts\MethodInterface;

final class Email extends MethodInterface
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $settings = config('services.newsletter.email');

        if ($settings['mode'] === 'LIVE') {
            $mode = $settings['data']['live'];
        } else {
            $mode = $settings['data']['sandbox'];
        }

        unset($settings['data']);

        $this->settings = array_merge($settings, $mode);
    }

    /**
     * @param array $users
     * @param array $data
     * @return mixed
     */
    public function to(array $users, array $data)
    {
        // TODO: Implement to() method.
    }

    /**
     * @param Model $user
     * @param array $data
     * @return mixed
     */
    public function toUser(Model $user, array $data)
    {
        // TODO: Implement toUser() method.
    }

    /**
     * @param string $topic
     * @param array $data
     * @return mixed
     */
    public function sentAll(string $topic, array $data)
    {
        // TODO: Implement sentAll() method.
    }
}
