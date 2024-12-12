<?php

namespace App\Modules\NutritionSpecialist\Controllers\Site;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class NutritionSpecialistNotesController extends AdminApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    // protected const REPOSITORY_NAME = 'NutritionSpecialistsNotes';

    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'nutritionSpecialists',
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
}
