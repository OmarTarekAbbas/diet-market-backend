<?php

namespace App\Modules\NutritionSpecialist\Controllers\Admin;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class NutritionSpecialistNotesController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'NutritionSpecialistsNotes',
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
     * Method schedule
     *
     * @param $id $id
     * @param Request $request
     *
     * @return void
     */
}
