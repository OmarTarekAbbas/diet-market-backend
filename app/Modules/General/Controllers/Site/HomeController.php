<?php

namespace App\Modules\General\Controllers\Site;

use Illuminate\Http\Request;
use App\Modules\General\Helpers\Visitor;
use HZ\Illuminate\Mongez\Managers\ApiController;

class HomeController extends ApiController
{
    /**
     * Get Home Data
     *
     * @param Request $request
     * @return Response|\Illuminate\Http\Response|string
     */
    public function index()
    {
        // $modules = $this->modulesRepository->listPublished([
        //     'app' => 'web',
        // ]);
        $customer = user();
        if (!$customer) {
            $customer = $this->guestsRepository->getByModel('customerDeviceId', Visitor::getDeviceId());
        }

        // $healthyData = ($customer) ? $this->healthyDatasRepository->getByModel('customerId', $customer->id) : $this->healthyDatasRepository->getByModel('customerDeviceId', Visitor::getDeviceId());
        if (user()) {
            $healthyData = $this->healthyDatasRepository->getByModel('customerId', $customer->id);
        } else {
            $healthyData = $this->healthyDatasRepository->getByModel('customerDeviceId', Visitor::getDeviceId());
        }

        // $products = $this->productsRepository->published([
        //     'paginate' => true,
        //     'itemsPerPage' => 5,
        //     // 'dietTypes' => $healthyData->dietTypes['id'],
        // ]);
        $dietTypes = $this->dietTypesRepository->get($healthyData->dietTypes);
        // dd($dietTypes);
        $categories = $this->categoriesRepository->published([
            'paginate' => true,
            'itemsPerPage' => 5,
            'type' => "products",
        ]);

        $restaurants = $this->restaurantsRepository->published([
            'paginate' => true,
            'itemsPerPage' => 5,
            'location' => $customer->location,
            'countItems' => true,
            'closedDb' => true,
            'countProductsDiet' => true, 
        ]);
        // dd($healthyData['healthInfo']['gender']);
        $clubs = $this->clubsRepository->published([
            'paginate' => true,
            'itemsPerPage' => 5,
            'location' => $customer->location,
            'gender' => $healthyData['healthInfo']['gender'],
        ]);

        $nutritionSpecialist = $this->nutritionSpecialistMangersRepository->published([
            'paginate' => true,
            'itemsPerPage' => 5,
            'location' => $customer->location,
        ]);


        return $this->success([
            'categories' => $categories,
            'restaurants' => $restaurants,
            'clubs' => $clubs,
            'nutritionSpecialist' => $nutritionSpecialist,
            'diet' => $dietTypes,
            'returnOrderStatus' => $this->settingsRepository->getSetting('ReturnedOrder', 'returnSystemStatus'),
        ]);
    }

    /**
     * It checks if the user has healthy data
     */
    public function checkHealthyData()
    {
        $customer = user();
        if (!$customer) {
            $customer = $this->guestsRepository->getByModel('customerDeviceId', Visitor::getDeviceId());
        }

        if (user()) {
            $healthyData = $this->healthyDatasRepository->getByModel('customerId', $customer->id);
        } else {
            $healthyData = $this->healthyDatasRepository->getByModel('customerDeviceId', Visitor::getDeviceId());
        }

        return $this->success([
            'isHealthyData' => ($healthyData) ? true : false,
        ]);
    }
}
