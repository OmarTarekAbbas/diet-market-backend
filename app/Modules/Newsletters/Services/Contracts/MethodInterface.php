<?php

namespace App\Modules\Newsletters\Services\Contracts;

use Illuminate\Support\Str;
use App\Modules\Newsletters\Services\Models\ServiceLog;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

abstract class MethodInterface
{
    abstract public function to(array $users, array $data);

    abstract public function toUser(Model $user, array $data);

    abstract public function sentAll(string $topic, array $data);

    /**
     * Log the given data
     *
     * @param array $data
     * @param array $settings
     * @return void
     */
    protected function log(array $data, array $settings)
    {
        $data = array_merge($data, [
            'type' => 'notification',
            'gateway' => 'firebase',
            'settings' => $settings,
            'userAgent' => request()->userAgent(),
        ]);

        $mapData = function ($data) use (&$mapData) {
            $details = [];

            foreach ($data as $key => $value) {
                $details[Str::camel(str_replace('.', '_', $key))] = is_array($value) || is_object($value) ? $mapData((array) $value) : $value;
            }

            return $details;
        };

        $details = $mapData($data);

        ServiceLog::create($details);
    }
}
