<?php

// Copyright
declare(strict_types=1);

namespace App\Modules\Newsletters\Services\Gateways;

use GuzzleHttp\Client;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;
use App\Modules\Newsletters\Services\Contracts\MethodInterface;

final class Firebase extends MethodInterface
{
    /**
     * Http guzzle client
     *
     * @var Client
     */
    private $http;

    /**
     * Gateway Settings
     *
     * @var array
     */
    private $settings = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $settings = config('services.newsletter.firebase');

        if ($settings['mode'] === 'LIVE') {
            $mode = $settings['data']['live'];
        } else {
            $mode = $settings['data']['sandbox'];
        }

        unset($settings['data']);

        $this->settings = array_merge($settings, $mode);

        $this->http = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "key={$this->settings['serverKey']}",
            ],
        ]);
    }

    public function toUser(Model $user, array $data)
    {
        return $this->to($user->getFireBaseDevicesIds(), $data);
    }

    /**
     * @param array $registrationIds
     * @param array $data
     * @return \Psr\Http\Message\ResponseInterface|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function to(array $registrationIds, array $data)
    {
        if (!$registrationIds) {
            return;
        }

        $requestBody = [
            'data' => $data['data'] ?? null,
            'registration_ids' => $registrationIds,
            'notification' => [
                'title' => $data['title'],
                'body' => $data['body'],
                'image' => $data['image'] ?? null,
                'sound' => $data['sound'] ?? 'default',
                'priority' => "high",
                'vibrate' => 1,
            ],
        ];

        $response = $this->http->post($this->settings['url'], [
            'json' => $requestBody,
        ]);

        $this->log([
            'channel' => 'sendTo',
            'registrationIds' => $registrationIds,
            'request' => $requestBody,
            'response' => json_decode($response->getBody()->getContents()),
        ], $this->settings);

        return $response;
    }

    /**
     * @param string $topic
     * @param array $data
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sentAll(string $topic, array $data)
    {
        $requestBody = [
            'to' => '/topics/' . $topic,
            'notification' => [
                'title' => $data['title'],
                'body' => $data['body'],
                'image' => $data['image'] ?? null,
                'sound' => $data['sound'] ?? 'default',
            ],
            'data' => $data['data'] ?? null,
        ];

        $response = $this->http->post($this->settings['url'], [
            'json' => $requestBody,
        ]);

        $this->log([
            'channel' => 'sendTopics',
            'topics' => $topic,
            'request' => $requestBody,
            'response' => json_decode($response->getBody()->getContents()),
        ], $this->settings);

        return $response;
    }
}
