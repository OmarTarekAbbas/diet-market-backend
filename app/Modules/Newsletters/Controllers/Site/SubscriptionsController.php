<?php

namespace App\Modules\Newsletters\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class SubscriptionsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'subscriptions';

    /**
     * {@inheritDoc}
     */
    public function subscribe(Request $request)
    {
        $email = (string) $request->email ?: '';

        if ($this->repository->getByModel('email', $email)) {
            return $this->badRequest('alreadySubscribed');
        }

        $this->repository->create([
            'email' => $email,
        ]);

        return $this->success();
    }
}
