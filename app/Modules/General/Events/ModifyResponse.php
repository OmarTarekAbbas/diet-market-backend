<?php

namespace App\Modules\General\Events;

class ModifyResponse
{
    /**
     * {@inheritDoc}
     */
    public function modifyResponse($response, $statusCode)
    {
        if ($statusCode == 200) {
            return [
                'data' => $response,
            ];
        }

        return $response;
    }
}
