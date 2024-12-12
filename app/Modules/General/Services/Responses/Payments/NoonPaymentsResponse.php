<?php

namespace App\Modules\General\Services\Responses\Payments;

use Illuminate\Support\Arr;
use App\Modules\Services\Contracts\PaymentGatewayResponse;

class NoonPaymentsResponse implements PaymentGatewayResponse
{
    /**
     * Hyper pay main statuses codes list
     *
     * @const string
     */
    const PENDING_TRANSACTION = 'PENDING';

    const SUCCESS_TRANSACTION = 'CAPTURED';

    const REJECTED_TRANSACTION = 'REJECTED';

    const FAILED_TRANSACTION = 'FAILED';

    const AUTHORIZED_TRANSACTION = 'AUTHORIZED';

    const INITIATED_TRANSACTION = 'INITIATED';

    /**
     * Response info
     *
     * @var array
     */
    private $responseData = [
        // sample of array contents
        'statusCode' => 'transaction status code',
        'message' => 'transaction message',
        'response' => 'full response',
        'responseStatusCode' => 'response status code',
    ];

    /**
     * Constructor
     *
     * @param array $responseData
     */
    public function __construct(array $responseData)
    {
        $this->responseData = $responseData;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(): string
    {
        switch ($statusCode = $this->getStatusCode()) {
            case static::PENDING_TRANSACTION:
            case static::INITIATED_TRANSACTION:
                return PaymentGatewayResponse::PENDING;
            case static::SUCCESS_TRANSACTION:
                return PaymentGatewayResponse::COMPLETED;
            case static::FAILED_TRANSACTION:
            case static::REJECTED_TRANSACTION:
                return PaymentGatewayResponse::FAILED;
            default:
                return PaymentGatewayResponse::FAILED;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): string
    {
        return $this->responseData['statusCode'];
    }

    /**
     * {@inheritdoc}
     */
    public function isPending(): bool
    {
        return $this->getStatusCode() === static::PENDING_TRANSACTION;
    }

    /**
     * {@inheritdoc}
     */
    public function isCompleted(): bool
    {
        return in_array($this->getStatusCode(), [static::SUCCESS_TRANSACTION]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasFailed(): bool
    {
        return !$this->isPending() && !$this->isCompleted();
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorMessage(): string
    {
        return $this->getMessage();
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage(): string
    {
        return $this->responseData['message'];
    }

    /**
     * Get payment status code
     *
     * @return string
     */
    public function getPaymentStatusCode(): string
    {
        $response = $this->getResponse();

        return $this->isCompleted() ? $response->result->order->status : $response->result->someWhereElse;
    }

    /**
     * Get full response
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->responseData['response'];
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key)
    {
        if (is_array($this->responseData['response'])) {
            return Arr::get($this->responseData['response'], $key);
        }

        return collect($this->responseData['response'])->get($key);
    }
}
