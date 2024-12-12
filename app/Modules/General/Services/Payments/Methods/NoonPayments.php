<?php

namespace App\Modules\General\Services\Payments\Methods;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Modules\Orders\Models\PaymentLog;
use App\Modules\Services\Models\ServiceLog;
use GuzzleHttp\Exception\BadResponseException;
use App\Modules\Services\Models\NoonPaymentLog;
use App\Modules\Services\Models\CardRegistration;
use App\Modules\Services\Contracts\PaymentGatewayResponse;
use App\Modules\Services\Contracts\PaymentMethodInterface;
use App\Modules\General\Services\Responses\Payments\NoonPaymentsResponse;

class NoonPayments implements PaymentMethodInterface
{
    /**
     * Payment Settings
     *
     * @var array
     */
    private $settings = [];

    /**
     * Request response
     *
     * @var Response
     */
    private $response;

    /**
     * Constructor
     */
    public function __construct()
    {
        $settings = config('services.payments.noonPayments');

        if ($settings['mode'] === 'LIVE') {
            $mode = $settings['data']['live'];
        } else {
            $mode = $settings['data']['sandbox'];
        }

        unset($settings['data']);

        $this->settings = array_merge($settings, $mode);
    }

    /**
     * Generate payment url
     *
     * @param int|string $orderId
     * @param $amount
     * @param $paymentMethod
     * @param null $userInfo
     * @return string hyper checkout id
     * @throws \Exception
     */
    public function initiate($orderId, $amount, $paymentMethod, $userInfo = null)
    {
        $user = $userInfo ?? user(); // Current User

        if (request()->type == 'food') {
            $userAddress = $user->cartMeal['shippingAddress']['address'] ?? null;
        } elseif (request()->type == 'products') {
            $userAddress = $user->cart['shippingAddress']['address'];
        } else {
            $userAddress = '';
        }

        $paymentMethod = strtoupper($paymentMethod);

        $saveCards = $this->option('saveCards');

        $cardRegistrations = CardRegistration::where('createdBy.id', $user->id)->latest()->limit(5)->get();
        // dd($amount);
        $options = [
            "apiOperation" => "INITIATE",
            "order" => [
                "reference" => "{$orderId}",
                "amount" => number_format($amount, 2, '.', ''),
                "currency" => $this->option('currency'),
                "name" => "Order From Diet Market : #{$orderId}",
                "channel" => $this->option('channel'),
                "category" => $this->option('category'),
                // "ipAddress" => "172.20.74.100",
            ],
            'billing' => [
                'address' => [
                    'street' => mb_substr($userAddress, 0, 50, 'utf-8') . '...' ,
                    // 'city' => $user->cart['shippingAddress']['city']['name'],
                    // 'city' => 'cairo',
                    // 'stateProvince' => $user->stateProvince,
                    'country' => 'SA',
                    // 'postalCode' => $user->postalCode,

                    // 'street' => $user->street,
                    // 'street2' => $user->street2,
                    // 'city' => $user->city,
                    // 'stateProvince' => $user->stateProvince,
                    // 'country' => 'SA',
                    // 'postalCode' => $user->postalCode,
                ],
                'contact' => [
                    'firstName' => $user->firstName ?? $user->name ?? '',
                    'lastName' => $user->lastName,
                    'phone' => $user->phoneNumber,
                    'mobilePhone' => $user->phoneNumber,
                    'email' => $user->email,
                ],
            ],
            'shipping' => [
                'address' => [
                    'street' => mb_substr($userAddress, 0, 50, 'utf-8') . '...',
                    'country' => 'SA',

                ],
                'contact' => [
                    'firstName' => $user->firstName ?? $user->name ?? '',
                    'lastName' => $user->lastName,
                    'phone' => $user->phoneNumber,
                    'mobilePhone' => $user->phoneNumber,
                    'email' => $user->email,
                ],
            ],
            "configuration" => [
                // "returnUrl" => route('noonPayment.returnUrl'),
                "returnUrl" => url('orders/confirm/noon'),
                "locale" => app()->getLocale(),
                "generateShortLink" => "true",
                "requiredCardHolderName" => "true",
                "tokenizeCc" => ($saveCards && $cardRegistrations->count() < 5),
                "styleProfile" => $this->option('styleProfile'),
            ],
        ];
        // dd($options);
        if ($saveCards) {
            foreach ($cardRegistrations as $cardRegistration) {
                $options["paymentTokens"][] = $cardRegistration->registrationId;
            }
        }
        $response = $this->send('/order', $options);

        if ($response->resultCode && $response->resultCode != 0) {
            throw  new \Exception($response->message);
        }

        $this->log([
            'channel' => 'initiate',
            'orderId' => $orderId,
            'request' => $options,
            'response' => $response,
            'noonPaymentId' => $response->result->order->id,
        ]);

        return $response->result;
    }

    /**
     * Get access token based on current environment
     *
     * @return string
     */
    private function getAccessToken(): string
    {
        $mode = ucwords(strtolower($this->option('mode')));
        $accessToken = $this->option('businessId') . '.' . $this->option('appName') . ':' . $this->option('appKey');
        // dd($accessToken);
        $accessToken = base64_encode($accessToken);

        return "Key_{$mode} {$accessToken}";
        // return "Key_Test ZGlldF9tYXJrZXQuNTZlZWUxZWM0YWM3NDM2OTk0N2ZhNjFhNzY5YTM4NTA6OWQ1ZjllZjZjN2Q5NDJhNTgxZDBiZWMzYjQ3ODAxYmM=";
    }

    /**
     * Check if current status of payment is sandbox mode
     *
     * @return bool
     */
    private function isSandboxMode(): bool
    {
        return !$this->isLiveMode();
    }

    /**
     * Check if current status of payment is sandbox mode
     *
     * @return bool
     */
    private function isLiveMode(): bool
    {
        return $this->option("mode") === 'LIVE';
    }

    /**
     * Get payment response status
     *
     * @param int $orderId
     * @param string $checkOutId
     * @param string $paymentMethod
     * @return PaymentGatewayResponse
     */
    public function confirm(int $orderId, string $checkOutId, string $paymentMethod): PaymentGatewayResponse
    {
        $paymentMethod = strtoupper($paymentMethod);
        $options = [
            "apiOperation" => "SALE",
            "order" => [
                "Id" => "{$checkOutId}",
            ],
        ];

        $content = $this->send($route = "/order", $options);
        $responseStatusCode = $this->response->getStatusCode();

        $responseData = [
            'response' => $content,
            'statusCode' => $content->result->order->status ?? NoonPaymentsResponse::FAILED,
            'message' => $content->message,
            'responseStatusCode' => $responseStatusCode,
        ];

        $response = new NoonPaymentsResponse($responseData);

        $this->log([
            'route' => $route,
            'paymentMethod' => $paymentMethod,
            'orderId' => $orderId,
            'request' => $options,
            'response' => $content,
            'noonPaymentId' => $checkOutId,
            'channel' => 'paymentStatus',
            'responseCode' => $this->response->getStatusCode(),
        ]);

        if ($response->isCompleted() && (!empty($response->getResponse()->result->paymentDetails->tokenIdentifier)) && ($registrationId = $response->getResponse()->result->paymentDetails->tokenIdentifier) && !CardRegistration::where('registrationId', $registrationId)->exists()) {
            $user = user();

            if (!$user) {
                $order = repo('orders')->get((int) $orderId);
                $user = repo('customers')->sharedinfo((int) $order['customer']['id']);
            }

            $paymentDetails = $response->getResponse()->result->paymentDetails;

            CardRegistration::create([
                'registrationId' => $registrationId,
                'card' => $response->getResponse()->result->paymentDetails->paymentInfo,
                'createdBy' => $user,
                'paymentBrand' => $paymentMethod,
                'default' => false,

                "instrument" => $paymentDetails->instrument,
                "tokenIdentifier" => $paymentDetails->tokenIdentifier,
                "cardAlias" => $paymentDetails->cardAlias,
                "mode" => $paymentDetails->mode,
                "integratorAccount" => $paymentDetails->integratorAccount,
                "paymentInfo" => $paymentDetails->paymentInfo,
                "paymentMechanism" => $paymentDetails->paymentMechanism,
                "payerInfo" => $paymentDetails->payerInfo,
                "brand" => $paymentDetails->brand,
                "scheme" => $paymentDetails->scheme,
                "expiryMonth" => $paymentDetails->expiryMonth,
                "expiryYear" => $paymentDetails->expiryYear,
                "isNetworkToken" => $paymentDetails->isNetworkToken,
                "cardType" => $paymentDetails->cardType,
                "cardCountry" => $paymentDetails->cardCountry,
                "cardCountryName" => $paymentDetails->cardCountryName,
            ]);
        }

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
            'type' => 'payment',
            'gateway' => 'noonPayments',
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

        // PaymentLog::create($details);

        ServiceLog::create($details);
    }

    /**
     * Send the given request
     *
     * @param string $route
     * @param array $options
     * @param string $requestMethod
     * @return array|object
     */
    private function send(string $route, array $options, string $requestMethod = 'POST')
    {
        $requestMethod = strtolower($requestMethod);
        $client = new Client([
            'http_errors' => true,
            'headers' => [
                "Authorization" => $this->getAccessToken(),
                'Content-Type' => 'application/json',
            ],
        ]);

        try {
            $this->response = $client->{$requestMethod}(rtrim($this->option('url'), '/') . $route, ['body' => json_encode($options)]);
        } catch (BadResponseException  $e) {
            $this->response = $e->getResponse();
        }

        return json_decode($this->response->getBody()->getContents());
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
     * Log the given data
     *
     * @param array $data
     * @return void
     */
    public function paymentLog(array $data)
    {
        $mapData = function ($data) use (&$mapData) {
            $details = [];

            foreach ($data as $key => $value) {
                $details[Str::camel(str_replace('.', '_', $key))] = is_array($value) || is_object($value) ? $mapData((array) $value) : $value;
            }

            return $details;
        };

        $details = $mapData($data);

        NoonPaymentLog::create($details);
    }
}
