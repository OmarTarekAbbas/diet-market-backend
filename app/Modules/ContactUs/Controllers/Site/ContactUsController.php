<?php

namespace App\Modules\ContactUs\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;

class ContactUsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'contactUs';

    /**
     * Get Website's Contact Info
     */
    public function contactUsInfo(Request $request)
    {
        return $this->success([
            'record' => [
                'location' => $this->settingsRepository->getSetting('general', 'location') ?? [],
                'email' => (string) $this->settingsRepository->getSetting('contact', 'email'),
                'address' => (string) $this->settingsRepository->getSetting('contact', 'address'),
                'whatsapp' => (string) $this->settingsRepository->getSetting('contact', 'whatsappNumber'),
                'phoneNumber' => (string) $this->settingsRepository->getSetting('contact', 'phoneNumber'),
                'facebook' => (string) $this->settingsRepository->getSetting('social', 'facebook'),
                'instagram' => (string) $this->settingsRepository->getSetting('social', 'instagram'),
                'twitter' => (string) $this->settingsRepository->getSetting('social', 'twitter'),
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function submit(Request $request)
    {
        $validator = $this->validateMessage($request);

        if ($validator->fails()) {
            return $this->badRequest($validator->errors());
        }

        $this->repository->create($request);

        return $this->success();
    }

    /**
     * Validate Contact Message
     */
    public function validateMessage(Request $request)
    {
        return Validator::make($request->all(), [
            // 'name' => 'required',
            // 'email' => 'required',
            'subject' => 'required',
            'type' => 'required',
            'message' => 'required',
            'department' => 'required|in:restaurants,clubs,nutrition,products',
        ]);
    }
}
