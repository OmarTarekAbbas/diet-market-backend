<?php

namespace App\Modules\Newsletters\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class NewslettersController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'newsletters';

    /**
     * {@inheritDoc}
     */
    public function subscribe(Request $request)
    {
        $email = (string) $request->email ?: '';

        if ($this->repository->isSubscribed($email)) {
            return $this->badRequest('alreadySubscribed');
        }

        $this->repository->subscribe($email);

        return $this->success();
    }
}
