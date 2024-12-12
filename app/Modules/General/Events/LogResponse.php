<?php

namespace App\Modules\General\Events;

use App\Modules\General\Models\ResponseLog;

class LogResponse
{
    /**
     * {@inheritDoc}
     */
    public function modifyResponse($response, $statusCode)
    {
        $request = request();

        $userInfo = null;

        if ($user = user()) {
            $userInfo = $user->sharedInfo();
            $userInfo['accountType'] = $user->accountType();
        }

        $response = json_decode(response($response)->getContent(), true);

        ResponseLog::create([
            'response' => $response,
            'statusCode' => $statusCode,
            'request' => $request->all(),
            'userAgent' => $request->userAgent(),
            'route' => $request->uri(),
            'ip' => $request->ip(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'user' => $userInfo,
        ]);

        return $response;
    }
}
