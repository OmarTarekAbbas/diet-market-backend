<?php

namespace App\Modules\Notifications\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use App\Modules\Users\Models\User;
use App\Modules\Services\Models\ServiceLog;

class PushNotifications
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

    private $key;

    private $baseUrl;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->key = config('services.firebase.serverKey');
        $this->baseUrl = config('services.firebase.baseUrl');

        $this->settings = [
            'key' => $this->key,
            'baseUrl' => $this->baseUrl,
        ];

        $this->http = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "key={$this->key}",
            ],
        ]);
    }

    /**
     * Push Notifications to the given user model
     *
     * @param Model $user
     * @param array $data
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function toUser($user, $data)
    {
        // return $this->to($user->getFireBaseDevicesIds(), $data);
        $androidDevices = $user->getAndroidFirebaseIds();
        $iosDevices = $user->getIOSFirebaseIds();
        // dd($androidDevices ,$iosDevices);
        return [
            $this->to($androidDevices, $data, 'Android'),
            $this->to($iosDevices, $data, 'iOS'),
        ];
    }

    /**
     * Send push notifications to the given registration ids
     *
     * @param array $registrationIds
     * @param array $data
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function to(array $registrationIds, array $data, string $appType)
    {
        // dd($registrationIds, $data , $appType);
        if (!$registrationIds) {
            return;
        }

        if (is_array($data['title'])) {
            foreach ($data['title'] as $title) {
                $data['data']['title' . strtoupper($title['localeCode'])] = $title['text'];
            }
            foreach ($data['body'] as $body) {
                $data['data']['body' . strtoupper($body['localeCode'])] = $body['text'];
            }
        }

        $notification = [
            'title' => $data['title'],
            'body' => $data['body'],
            'image' => $data['image'] ?? null,
            'sound' => $data['sound'] ?? 'default',
            'priority' => "high",
            'vibrate' => 1,
        ];

        $dataList = isset($data['data']) ? array_merge($notification, $data['data']) : $notification;

        // $requestBody = [
        //     'data' => $dataList,
        //     'registration_ids' => $registrationIds,
        //     'mutable_content' => true,
        //     'notification' => $dataList,
        // ];

        $requestBody = [
            'registration_ids' => $registrationIds,
            'mutable_content' => true,
        ];

        if ($appType === 'Android') {
            $requestBody['data'] = $dataList;
        } else {
            $requestBody['notification'] = $dataList;
        }


        $response = $this->http->post($this->baseUrl, [
            'json' => $requestBody,
        ]);

        $this->log([
            'channel' => 'sendTo',
            'registrationIds' => $registrationIds,
            'request' => $requestBody,
            'response' => json_decode($response->getBody()->getContents()),
        ]);

        return $response;
    }

    /**
     * Push Notifications to the given topic
     *
     * @param string $topic
     * @param array $data
     * @return \Psr\Http\Message\ResponseInterface[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendTopic($topic, $data)
    {
        $androidResponse = $this->sendTopicToAndroid($topic, $data);
        $iosResponse = $this->sendTopicToiOS($topic, $data);

        return [$androidResponse, $iosResponse];
    }

    /**
     * Send Topic to android devices by appending `Android` to the end of the topic
     *
     * @param string $topic
     * @param array $data
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendTopicToAndroid(string $topic, array $data)
    {
        if (is_array($data['title'])) {
            foreach ($data['title'] as $title) {
                $data['data']['title' . strtoupper($title['localeCode'])] = $title['text'];
            }

            foreach ($data['body'] as $body) {
                $data['data']['body' . strtoupper($body['localeCode'])] = $body['text'];
            }
        }

        $notification = [
            'title' => $data['title'],
            'body' => $data['body'],
            'image' => $data['image'] ?? null,
            'sound' => $data['sound'] ?? 'default',
            'priority' => "high",
            'vibrate' => 1,
        ];

        $dataList = isset($data['data']) ? array_merge($notification, $data['data']) : $notification;

        $requestBody = [
            'to' => '/topics/' . $topic . 'Android',
            'mutable_content' => true,
            'data' => $dataList,
            'notification' => null,
        ];

        $response = $this->http->post($this->baseUrl, [
            'json' => $requestBody,
        ]);

        $this->log([
            'channel' => 'sendTopics',
            'topics' => $topic,
            'device' => 'Android',
            'request' => $requestBody,
            'response' => json_decode($response->getBody()->getContents()),
        ]);

        return $response;
    }

    /**
     * Send Topic to iOS devices by appending `iOS`  to the end of the topic
     *
     * @param string $topic
     * @param array $data
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendTopicToiOS(string $topic, array $data)
    {
        if (is_array($data['title'])) {
            foreach ($data['title'] as $title) {
                $data['data']['title' . strtoupper($title['localeCode'])] = $title['text'];
            }

            foreach ($data['body'] as $body) {
                $data['data']['body' . strtoupper($body['localeCode'])] = $body['text'];
            }
        }


        $notification = [
            'title' => $data['title'],
            'body' => $data['body'],
            'image' => $data['image'] ?? null,
            'sound' => $data['sound'] ?? 'default',
            'priority' => "high",
            'vibrate' => 1,
        ];

        $dataList = isset($data['data']) ? array_merge($notification, $data['data']) : $notification;

        $requestBody = [
            'to' => '/topics/' . $topic . 'iOS',
            'mutable_content' => true,
            'data' => $dataList,
            'notification' => $dataList,
        ];

        $response = $this->http->post($this->baseUrl, [
            'json' => $requestBody,
        ]);

        $this->log([
            'channel' => 'sendTopics',
            'topics' => $topic,
            'device' => 'iOS',
            'request' => $requestBody,
            'response' => json_decode($response->getBody()->getContents()),
        ]);

        return $response;
    }

    /**
     * Log the given data
     *
     * @param array $data
     * @return void
     */
    private function log(array $data)
    {
        $data = array_merge($data, [
            'type' => 'notification',
            'gateway' => 'firebase',
            'settings' => $this->settings,
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
