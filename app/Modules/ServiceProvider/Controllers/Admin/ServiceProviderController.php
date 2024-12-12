<?php

namespace App\Modules\ServiceProvider\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ServiceProviderController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'serviceProviders',
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
     * Method serviceProviderAccepted
     *
     * @param $id $id
     *
     * @return void
     */
    public function serviceProviderAccepted($id)
    {
        $this->repository->serviceProviderAccepted($id);

        return $this->success([
            'record' => 'success',
        ]);
    }

    /**
     * Method serviceProviderRejected
     *
     * @param $id $id
     *
     * @return void
     */
    public function serviceProviderRejected($id)
    {
        $this->repository->serviceProviderRejected($id);

        return $this->success([
            'record' => 'success',
        ]);
    }
}
