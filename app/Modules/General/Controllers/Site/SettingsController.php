<?php

namespace App\Modules\General\Controllers\Site;

use Illuminate\Http\Request;
use App\Modules\General\Helpers\Currency;
use HZ\Illuminate\Mongez\Managers\ApiController;

class SettingsController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $repository = 'settings';

    /**
     * Get Home Data
     *
     * @return Response
     */
    public function index()
    {
        $settings = $this->repository->listByGroup('general', 'social', 'contact');

        $responseData = [];

        foreach ($settings as $setting) {
            if (in_array($setting->group, ['general', 'contact'])) {
                $responseData[$setting->name] = $setting->value;
            } else {
                $responseData[$setting->group][$setting->name] = $setting->value;
            }
        }

        return $this->success([
            'settings' => $responseData,
            'currencies' => array_values(Currency::getCurrencies()),
        ]);
    }

    /**
     * Method returnOrderPolicy
     *
     * @param Request $request
     *
     * @return void
     */
    public function returnOrderPolicy(Request $request)
    {
        $returnOrderPolicy = $this->repository->getSetting('ReturnedOrder', 'returnOrderPolicy');
        if (app()->getLocale() == 'en') {
            $returnOrderPolicy = $returnOrderPolicy[1];
        } else {
            $returnOrderPolicy = $returnOrderPolicy[0];
        }
        // dd($returnOrderPolicy["text"]);
        return $this->success([
            'returnOrderPolicy' => $returnOrderPolicy["text"],
        ]);
    }
}
