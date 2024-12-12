<?php

namespace App\Modules\General\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class SearchController extends ApiController
{
    /**
     * Method index
     *
     * @param Request $request
     * Search all item for websit
     * @return void
     */
    public function index(Request $request)
    {
        if ($request->type == 'nutritionSpecialists') {
            $request->type = 'nutritionSpecialistMangers';
            $typeProducts = '';
        }
        if ($request->type == 'restaurants') {
            $typeProducts = '';
        }
        if ($request->type == 'clubs') {
            $typeProducts = '';
        }
        if ($request->type == 'meals') {
            $request->type = 'productMeals';
            $typeProducts = 'food';
        }
        if ($request->type == 'products') {
            $typeProducts = 'products';
        }

        return $this->success([
            'record' => repo($request->type)->published([
                'paginate' => true,
                'itemsPerPage' => $request->itemsPerPage ?? 5,
                'name' => $request->name,
                'page' => $request->page,
                'type' => $typeProducts,
            ]),
            'paginationInfo' => repo($request->type)->getPaginateInfo(),
        ]);
    }
}
