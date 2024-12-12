<?php

namespace App\Modules\Modules\Controllers\Admin;

use Illuminate\Http\Request;
use App\Rules\DataInModuleRule;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ModulesController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'modules',
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

    // protected function storeValidation($request): array
    // {
    //     return [
    //         'type' => 'required|in:sliders,categories,newArrivals,bestSeller,specials,products,banners',
    //         // 'data' => ['required' , new DataInModuleRule()]
    //     ];
    // }

    // protected function updateValidation($id, Request $request): array
    // {
    //     return [
    //         'type' => 'required|in:sliders,categories,newArrivals,bestSeller,specials,products,banners',
    //         // 'data' => ['required' , new DataInModuleRule()]
    //     ];
    // }
}
