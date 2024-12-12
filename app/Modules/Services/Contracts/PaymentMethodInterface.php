<?php

namespace App\Modules\Services\Contracts;

interface PaymentMethodInterface
{
    /**
     * Payment transaction is pending
     *
     * @const string
     */
    const PENDING = 'pending';

    /**
     * Payment transaction is completed
     *
     * @const string
     */
    const SUCCESS = 'success';

    /**
     * Payment transaction is failed
     *
     * @const string
     */
    const FAILED = 'failed';

    /**
     * Initiate Payment
     * Payment methods may be passed as a payment gateway may provide multiple payment methods in one gate
     *
     * @param int $orderId
     * @param float $amount
     * @param string|null $paymentMethod
     */
    public function initiate(int $orderId, float $amount, string $paymentMethod);

    /**
     * Confirm Payment
     *
     * @param int $orderId
     * @param string $checkOutId
     * @param string|null $paymentMethod
     */
    public function confirm(int $orderId, string $checkOutId, string $paymentMethod);
}
