<?php

namespace App\Modules\Services\Exceptions;

use App\Modules\Services\Contracts\PaymentGatewayResponse;

class InvalidPaymentException extends \Exception
{
    /**
     * Payment Response
     *
     * @var PaymentGatewayResponse
     */
    protected $response;

    /**
     * Constructor
     *
     * @param  PaymentGatewayResponse $response
     */
    public function __construct(PaymentGatewayResponse $response)
    {
        $this->response = $response;

        parent::__construct($this->response->getMessage());
    }

    /**
     * Get response
     *
     * @return PaymentGatewayResponse
     */
    public function response(): PaymentGatewayResponse
    {
        return $this->response;
    }
}
