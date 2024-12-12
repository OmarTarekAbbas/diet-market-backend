<?php

namespace App\Modules\General\Controllers\Site;

use HZ\Illuminate\Mongez\Managers\ApiController;

class TermsAndConditionDeliverys extends ApiController
{
    /**
     * Method termsOfUseDelivery
     *
     * @return void
     */
    public function termsOfUseDelivery()
    {
        return $this->success([
            'record' => $this->settingsRepository->getSetting('deliveryMen', 'termsOfUseDelivery') ?? '',
        ]);
    }

    /**
     * Method privacyPolicyDelivery
     *
     * @return void
     */
    public function privacyPolicyDelivery()
    {
        return $this->success([
            'record' => $this->settingsRepository->getSetting('deliveryMen', 'privacyPolicyDelivery') ?? '',
        ]);
    }

    /**
     * Method conditionsWorkingDelivery
     *
     * @return void
     */
    public function conditionsWorkingDelivery()
    {
        return $this->success([
            'record' => $this->settingsRepository->getSetting('deliveryMen', 'conditionsWorkingDelivery') ?? '',
        ]);
    }
}
