<?php

namespace App\Modules\DeliveryMen\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class DeliveryMenController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'deliveryMen';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [];

        return $this->success([
            'records' => $this->repository->list($options),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function show($id, Request $request)
    {
        return $this->success([
            'record' => $this->repository->get($id),
        ]);
    }

    /**
     * Method deliveryMenWebView
     *
     * @param Request $request
     *
     * @return url
     */
    public function deliveryMenWebView(Request $request)
    {
        return $this->success([
            'records' => env('APP_URL') . '/deliveryMen/register',
        ]);
    }

    /**
     * Method allSettingDeliveryMen
     *
     * @param Request $request
     *
     * @return
     */
    public function allSettingDeliveryMen(Request $request)
    {
        return $this->success([
            'activateTheDeliveryMenRegistrationSetting' => $this->settingsRepository->getSetting('deliveryMen', 'registrationDeliveryMen'),

            'splashLogoDeliveryMen' => url($this->settingsRepository->getSetting('deliveryMenImage', 'splashLogoDeliveryMen')),
            'appLogoDeliveryMen' => url($this->settingsRepository->getSetting('deliveryMenImage', 'appLogoDeliveryMen')),

            'updateLocationForSecond' => $this->settingsRepository->getSetting('deliveryMen', 'updateLocationForSecondDeliveryMen'),
            'deliveryMenUrlChat' => $this->settingsRepository->getSetting('deliveryMen', 'deliveryMenUrlChat'),
            'countDeliveryNotifications' => user()->totalNotifications ?? 0,
            'walletBalance' => user()->walletBalance ?? 0,
        ]);
    }
}
