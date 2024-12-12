<?php

namespace App\Modules\ClubBookings\Controllers\Admin;

use App\Modules\ClubBookings\Traits\ChangeStatus;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ClubBookingsController extends AdminApiController
{
    use ChangeStatus;

    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        
        'repository' => 'clubBookings',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [],
            'store' => [],
            'update' => [],
        ],
    ];

    /**
     * Make custom validation for store.
     *
     * @param mixed $request
     *
     * @return array
     */
    protected function storeValidation($request): array
    {
        $rules = [
            'clubBranch' => ['required'],
        ];

        return $rules;
    }

    /**
     * Make custom validation for store.
     *
     * @param int $id
     * @param mixed $request
     * @return array
     */
    protected function updateValidation($id, $request): array
    {
        return [
            'clubBranch' => ['required'],
        ];
    }
}
