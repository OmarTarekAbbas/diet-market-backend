<?php

// Copyright
declare(strict_types=1);

namespace App\Modules\Newsletters\Services\Gateways;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Modules\General\Models\ServiceLog;

class SMS
{
    /**
     * Gateway Settings
     *
     * @var array
     */
    private $settings = [];

    public $url;

    public $username;

    public $password;

    public $sander_name;

    /**
     * Constructor
     */
    public function __construct()
    {
        // $this->url = config('services.sms.url');
        // $this->username = config('services.sms.username');
        // $this->password = config('services.sms.password');
        // $this->sander_name = config('services.sms.sanderName');

        // $this->settings = [
        //     'url' => $this->url,
        //     'username' => $this->username,
        //     'password' => $this->password,
        //     'sander_name' => $this->sander_name,
        // ];

        $settings = config('services.newsletter.sms');

        if ($settings['mode'] === 'LIVE') {
            $mode = $settings['data']['live'];
        } else {
            $mode = $settings['data']['sandbox'];
        }

        unset($settings['data']);

        $this->settings = array_merge($settings, $mode);
    }

    /**
     * Get settings value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function option(string $key, $default = null)
    {
        return Arr::get($this->settings, $key, $default);
    }

    /**
     * Send message to the given numbers
     *
     * @param string $message
     * @param $number
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(string $message, $number)
    {
        if (!Str::startsWith($number, '+')) {
            if (Str::startsWith($number, '0')) {
                $number = substr($number, 1);
            }
            if (!Str::startsWith($number, '966')) {
                $number = '966' . $number;
            }
            $number = '+' . $number;
        }

        $request = new Client;

        $message = urlencode($message);
        $fullURl = "{$this->option('url')}?username={$this->option('username')}&password={$this->option('password')}&numbers={$number}&message={$message}&sender={$this->option('sanderName')}&unicode=E&return=json";
        // dd($fullURl);
        try {
            $response = $request->get($fullURl);

            $response = json_decode($response->getBody()->getContents());

            $this->log([
                'channel' => 'sendSMS',
                'number' => $number,
                'url' => $fullURl,
                'response' => $response,
            ]);

            return (isset($response->Code) && $response->Code == "100");
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage() . $exception->getTraceAsString());
        }
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
            'type' => 'sms',
            'gateway' => 'oursms',
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
