<?php

namespace App\Modules\Restaurants\Repositories;

use App\Modules\Cities\Models\City;
use App\Modules\General\Services\Slugging;
use App\Modules\Categories\Models\Category;
use App\Modules\Restaurants\Models\Restaurant as Model;
use App\Modules\Restaurants\Filters\Restaurant as Filter;
use App\Modules\Restaurants\Resources\Restaurant as Resource;
use App\Modules\TypeOfFoodRestaurant\Models\TypeOfFoodRestaurant;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class RestaurantsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'restaurants';

    /**
     * Model class name
     *
     * @const string
     */
    const MODEL = Model::class;

    /**
     * Resource class name
     *
     * @const string
     */
    const RESOURCE = Resource::class;

    /**
     * Set the columns of the data that will be auto filled in the model
     *
     * @const array
     */
    const DATA = ['name', 'logoText', 'address', 'workTimes', 'metaTag', 'KeyWords'];

    // 'categories','rating','totalRating','restaurantStatus'
    /**
     * Auto save uploads in this list
     * If it's an indexed array, in that case the request key will be as database column name
     * If it's associated array, the key will be request key and the value will be the database column name
     *
     * @const array
     */
    const UPLOADS = ['logoImage', 'commercialRegisterImage', 'cover'];

    /**
     * Auto fill the following columns as arrays directly from the request
     * It will encoded and stored as `JSON` format,
     * it will be also auto decoded on any database retrieval either from `list` or `get` methods
     *
     * @const array
     */
    const ARRAYBLE_DATA = [];

    /**
     * Set columns list of integers values.
     *
     * @cont array
     */
    const INTEGER_DATA = ['commercialRegisterNumber', 'minimumOrders', 'countItems'];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = ['rating', 'totalRating', 'deliveryValue', 'rateDelivery', 'totalRatingDelivery', 'profitRatio', 'cashOnDeliveryValue'];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = ['published', 'delivery', 'isBusy', 'cashOnDelivery'];

    /**
     * Geo Location data
     *
     * @const array
     */
    const LOCATION_DATA = ['location'];

    /**
     * Set columns list of date values.
     *
     * @cont array
     */
    const DATE_DATA = [];

    /**
     * Set the columns will be filled with single record of collection data
     * i.e [country => CountryModel::class]
     *
     * @const array
     */
    const DOCUMENT_DATA = [
        'city' => City::class,
    ];

    /**
     * Set the columns will be filled with array of records.
     * i.e [tags => TagModel::class]
     *
     * @const array
     */
    const MULTI_DOCUMENTS_DATA = [
        'categories' => Category::class,
        'typeOfFoodRestaurant' => TypeOfFoodRestaurant::class,
    ];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array
     */
    const WHEN_AVAILABLE_DATA = ['name', 'logoText', 'address', 'logoImage', 'commercialRegisterImage', 'commercialRegisterNumber', 'workTimes', 'minimumOrders', 'city', 'delivery', 'published', 'isBusy', 'rating', 'totalRating', 'categories', 'typeOfFoodRestaurant', 'deliveryValue', 'rateDelivery', 'totalRatingDelivery', 'profitRatio', 'countItems', 'metaTag', 'KeyWords'];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'boolean' => ['published'],
        'like' => [
            'name' => 'name.text',
        ],
        'inInt' => ['id'],
    ];

    /**
     * Set of the parents repositories of current repo
     *
     * @const array
     */
    const CHILD_OF = [];

    /**
     * Set of the children repositories of current repo
     *
     * @const array
     */
    const PARENT_OF = [];

    /**
     * Set all filter class you will use in this module
     *
     * @const array
     */
    const FILTERS = [
        Filter::class,
    ];

    /**
     * Determine wether to use pagination in the `list` method
     * if set null, it will depend on pagination configurations
     *
     * @const bool
     */
    const PAGINATE = true;

    /**
     * Number of items per page in pagination
     * If set to null, then it will taken from pagination configurations
     *
     * @const int|null
     */
    const ITEMS_PER_PAGE = 15;

    /**
     * Set any extra data or columns that need more customizations
     * Please note this method is triggered on create or update call
     *
     * @param   mixed $model
     * @param   \Illuminate\Http\Request $request
     * @return  void
     */
    protected function setData($model, $request)
    {
        // if ($request->unitTesting) {
        //     $this->createDataForUnitTest($model, $request);
        //     return;
        // }
        if ($request->delivery == false) {
            $model->deliveryValue = null;
        }
        $this->setSlug($model, $request);
    }

    /**
     * Method setSlug
     *
     * @param $model $model
     * @param $request $request
     *
     * @return void
     */
    public function setSlug($model, $request)
    {
        if (!$model['slug']) {
            $slug = [];

            foreach ($request->name as $name) {
                $slug[] = [
                    'text' => $model->getId() . '/' . Slugging::make($name['text'], $name['localeCode']),
                    'localeCode' => $name['localeCode'],
                ];
            }

            $model->slug = $slug;
        }
    }

    /**
     * Method onSave
     *
     * @param $model $model
     * @param $request $request
     * @param $oldModel $oldModel
     *
     * @return void
     */
    public function onSave($model, $request, $oldModel = null)
    {
        $this->typeOfFoodRestaurantsRepository->updateCount($model->typeOfFoodRestaurant);
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        if ($location = $this->option('location')) {
            $km = $this->settingsRepository->getSetting('restaurant', 'searchArea') ?: (int) env('SEARCH_AREA');
            // dd($km);
            $this->query->whereLocationNear('location', [(float) $location['coordinates'][0] /* latitude */, (float) $location['coordinates'][1]/* longitude */], $km);
        }
        if ($this->option('countItems')) {
            // dd('sdsd');
            $this->query->where('countItems', '>', 0);
        }
        
        if ($this->option('closedDb')) {
            if (user() && user()->accountType() === 'customer') {
                $this->query->where('closedDb', false);
            }
        }

        if ($this->option('countProductsDiet')) {
            if (user() && user()->accountType() === 'customer') {
                $this->query->where('countProductsDiet', '>', 0);
            }
        }
    }

    /**
     * update rating record
     * @return void
     */
    // public function updateRating($id, $rating): void
    // {
    //     $provider = $this->getModel($id);
    //     if ($provider) {
    //         $provider->rating = $rating;
    //         $provider->save();
    //     }
    // }

    /**
     * Method getRepositoryForCategories
     *
     * @param $id $id
     * get Repository For Categories
     * @return void
     */
    public function getRepositoryForCategories($id)
    {
        return $this->categoriesRepository->wrapMany($this->categoriesRepository->getQuery()->where('restaurant.id', (int) $id)->where('type', 'food')->get());
        // return ;
    }


    /**
     * Method makeDataForUnit
     *
     * @param $model $model
     * @param $request $request
     * make Data For Unit
     * @return void
     */
    // public function makeDataForUnit($model, $request)
    // {
    //     $model->name = $request->name;
    //     $model->logoText = $request->logoText;
    //     $model->address = $request->address;
    //     $model->workTimes = $request->workTimes;
    //     $model->commercialRegisterNumber = $request->commercialRegisterNumber;
    //     $model->minimumOrders = $request->minimumOrders;
    //     $model->published = $request->published;
    //     $model->delivery = $request->delivery;
    // }

    /**
     * Method createDataForUnitTest
     *
     * @param $model $model
     * @param $request $request
     * create Data For Unit Test
     * @return void
     */
    // public function createDataForUnitTest($model, $request)
    // {
    //     if ($request->method() == 'POST') {
    //         $this->makeDataForUnit($model, $request);
    //     }

    //     if ($request->method() == 'PUT') {
    //         $this->makeDataForUnit($model, $request);
    //     }
    // }

    /**
     * Method getCategoryForRestaurants
     *
     * @param $id $id
     *
     * @return void
     */
    public function getCategoryForRestaurants($id)
    {
        $restaurants = $this->restaurantsRepository->get($id);
        // $categoryRestaurants1 = [];
        // foreach ($restaurants->categories as $key => $cat) {
        //     $options['id'] = (int) $cat['id'];
        //     $categoryRestaurants1[]  =  $this->categoriesRepository->listPublished($options);
        // }
        // dd($categoryRestaurants1);
        $categoryRestaurants = $restaurants->categories;

        $categories = [];

        foreach ($categoryRestaurants as $key => $categoryRestaurant) {
            if ($categoryRestaurant['published'] == false) {
                continue;
            }

            $products = $this->productsRepository->listPublished([
                'category' => $categoryRestaurant['id'],
                'type' => 'food',
                'restaurant' => $id,
                'paginate' => false,
            ]);

            // if (!$products) {
            //     unset($categoryRestaurants[$key]);
            //     continue;
            // }

            $categoryRestaurant['products'] = $products;
            $categories[] = $categoryRestaurant;
        }

        return $categories;
    }

    public function noFoodInRestaurants(int $id)
    {
        $restaurants = $this->restaurantsRepository->get($id);

        $categoryRestaurants = $restaurants->categories;

        $categories = [];

        foreach ($categoryRestaurants as $key => $categoryRestaurant) {
            if ($categoryRestaurant['published'] == false) {
                continue;
            }
            $products = $this->productsRepository->listPublished([
                'category' => $categoryRestaurant['id'],
                'type' => 'food',
                'restaurant' => $id,
                'paginate' => false,
            ]);
            $categoryRestaurant['products'] = $products;
            $countProductsDieUpdate = $this->getQuery()->where('id', $id)->first();
            $countProductsDieUpdate->countProductsDiet = collect($categoryRestaurant['products'])->count();
            $countProductsDieUpdate->save();
            $categories[] = $categoryRestaurant;
        }
        return $categories;
    }

    /**
     * Method isClosedRestaurants
     *
     * @param $id $id
     *
     * @return void
     */
    public function isClosedRestaurants($id)
    {
        return $this->restaurantsRepository->get($id);
    }

    /**
     * Method checkDistance
     *
     * @param $request $request
     *
     * @return void
     */
    public function checkDistance($request)
    {
        $getkm = $this->curlGoogleMaps($request);
        $getkm = $getkm;
        $searchArea = $this->settingsRepository->getSetting('restaurant', 'searchArea') ?: 50;
        $getkm = trim($getkm, ' km');
        $getkm = intval(preg_replace('/[^\d.]/', '', $getkm));

        return ($getkm > (float) $searchArea) ? false : true;
    }

    /**
     * Method betweenTheTwoPoints
     *
     * @param $request $request
     *
     * @return void
     */
    public function betweenTheTwoPoints($request)
    {
        $getkm = $this->curlGoogleMaps($request);

        return $getkm;
    }

    /**
     * Method curlGoogleMaps
     *
     * @param $request $request
     *
     * @return void
     */
    public function curlGoogleMaps($request)
    {
        $restaurant = $this->restaurantsRepository->getQuery()->where('id', (int) $request->restaurant)->first();

        $la1 = $restaurant->location['coordinates'][0];
        $la2 = $restaurant->location['coordinates'][1];
        $la3 = (float) $request->lat;
        $la4 = (float) $request->lng;
        $key = KEY_GOOGLE_MAB;
        $URL = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins={$la1},{$la2}&destinations={$la3},{$la4}&key={$key}";
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = curl_exec($curl);
        $decodeResponseLocation = json_decode($response, true); // Set second argument as TRUE
        $rowResponseLocation = $decodeResponseLocation['rows'];
        foreach ($rowResponseLocation as $row) {
            $elementKm = $row['elements'];
            foreach ($elementKm as $km) {
                $getkm = $km['distance']['text'] ?? 0;
            }
        }

        return $getkm;
    }

    /**
     * Method checkDistanceAddressCart
     *
     * @param $request $request
     *
     * @return void
     */
    public function checkDistanceAddressCart($address, $restaurant)
    {
        $km = $this->settingsRepository->getSetting('restaurant', 'searchArea') ?: 50;

        $restaurant = $this->restaurantsRepository->getQuery()->where('id', (int) $restaurant['id']);

        return $restaurant->whereLocationNear('location', [(float) $address['location']['coordinates'][0] /* latitude */, (float) $address['location']['coordinates'][1]/* longitude */], $km)->first();
    }

    /**
     * update Rate
     *
     * @param int $restaurantId
     * @param float $avg
     * @param int $total
     */
    public function updateRate(int $restaurantId, float $avg, int $total)
    {
        $restaurant = $this->getModel($restaurantId);
        $restaurant->update([
            'rating' => $avg,
            'totalRating' => $total,
        ]);
    }

    /**
     * update Rate
     *
     * @param int $restaurantId
     * @param float $avg
     * @param int $total
     */
    public function updateRateDelivery(int $restaurantId, float $avg, int $total)
    {
        $restaurant = $this->getModel($restaurantId);
        $restaurant->update([
            'rateDelivery' => $avg,
            'totalRatingDelivery' => $total,
        ]);
    }

    /**
     * Method updateTransaction
     *
     * @param int $storeId
     *
     * @return void
     */
    public function updateTransaction(int $storeId)
    {
        $store = $this->getModel($storeId);

        // dd($store);
        $type = 'food';
        $store->transaction = [
            'totalRequired' => $totalRequired = $this->transactionsRepository->getTotalRequired($storeId, $type),

            'profitRatio' => $this->transactionsRepository->getProfitRatio($storeId, $type),

            'totalOrder' => $this->transactionsRepository->getTotalOrder($storeId, $type),

        ];

        $store->save();
    }

    /**
     * Method onUpdate
     *
     * @param $model $model
     * @param $request $request
     * @param $oldModel $oldModel
     *
     * @return void
     */
    public function onUpdate($model, $request, $oldModel)
    {
        $this->productMealsRepository->updateProductMeals($model);
        $this->restaurantManagersRepository->updateRestaurantManagers($model);
    }

    /**
     * Method onDelete
     *
     * @param $model $model
     * @param $request $request
     * @param $oldModel $oldModel
     *
     * @return void
     */
    public function onDelete($model, $request, $oldModel = null)
    {
        $this->typeOfFoodRestaurantsRepository->deleteCount($model->typeOfFoodRestaurant);
        $this->productMealsRepository->deleteProductMeals($model);
        $this->restaurantManagersRepository->deleteRestaurantManagers($model);
    }

    /**
     * Update category info on category update
     *
     * @param Category $category
     * @return void
     */
    public function updateCategoryInfo($category)
    {
        $restaurants = Model::where('categories.id', $category->id)->get();
        // dd($restaurants);
        foreach ($restaurants as $restaurant) {
            $restaurant->reassociate($category, 'categories')->save();
        }
    }

    /**
     * Method deleteCategoryInfo
     *
     * @param $category $category
     *
     * @return void
     */
    public function deleteCategoryInfo($category)
    {
        $restaurants = Model::where('categories.id', $category->id)->get();
        foreach ($restaurants as $restaurant) {
            $restaurant->disassociate($category, 'categories')->save();
        }
    }

    /**
     * Method getPublishedRestaurants
     *
     * @return void
     */
    // public function getPublishedRestaurants($id)
    // {
    //     $aRestaurantThatHasAProduct = $this->productsRepository->published([
    //         'type' => 'food',
    //         'restaurant' => $id,
    //     ]);
    //     if ($aRestaurantThatHasAProduct->count() == 0) {
    //         $restaurant =  Model::find($id);
    //         $restaurant->published = false;
    //         $restaurant->save();
    //     }
    // }

    /**
     * Method UpdateProduct
     *
     * @param $model $model
     * @param $request $request
     * @param $oldModel $oldModel
     *
     * @return void
     */
    public function UpdateProduct($model, $request, $oldModel)
    {
        if ($model['restaurant'] && (int) $request->restaurant != $oldModel['restaurant']['id']) {
            $updateCount = Model::find((int) $request->restaurant);
            $updateCount->countItems = $updateCount->countItems + 1;
            $updateCount->save();

            $updateCount = Model::find((int) $oldModel['restaurant']['id']);
            $updateCount->countItems = $updateCount['countItems'] - 1;
            $updateCount->save();
        } elseif ((int) $request->restaurant == $oldModel['restaurant']['id']) {
            $updateCount = Model::find((int) $model['restaurant']['id']);
            $updateCount->countItems = $updateCount['countItems'];
            $updateCount->save();
        } else {
            $updateCount = Model::find((int) $model['restaurant']['id']);
            $updateCount->countItems = $updateCount['countItems'] + 1;
            $updateCount->save();
        }
    }

    public function createProduct($model, $request)
    {
        $updateCount = Model::find((int) $request->restaurant);
        $updateCount->countItems = $updateCount->countItems + 1;
        $updateCount->save();
    }

    /**
     * Method removeProduct
     *
     * @param $model $model
     *
     * @return void
     */
    public function removeProduct($model)
    {
        $updateCount = Model::find((int) $model['restaurant']['id']);
        $updateCount->countItems = $updateCount->countItems - 1;
        $updateCount->save();
    }

    /**
     * Method deleteStoreOrder
     *
     * @param $id $id
     *
     * @return void
     */
    public function deleteResturantOrder($id)
    {
        return $this->ordersRepository->getQuery()->where('restaurantManager.restaurant.id', $id)->first();
    }

    /**
     * Method get Last Order Is Not Review
     *
     * @param $customer
     *
     * @return void
     */
    public function getLastOrderIsNotReview($customer)
    {
        $getLastOrderIsNotReview = $this->ordersRepository->getQuery()
            ->where('customer.id', (int) $customer->id)
            ->where('productsType', 'food')
            ->where('status', 'completed')
            ->where('restaurantsReviews', null)
            ->where('isGetLastOrderIsNotReview', false)
            ->orderBy('id', 'desc')->first();
        if ($getLastOrderIsNotReview) {
            $this->ordersRepository->getQuery()->where('id', (int) $getLastOrderIsNotReview->id)->update([
                'isGetLastOrderIsNotReview' => true,
            ]);
        }
        if ($getLastOrderIsNotReview) {
            return $this->ordersRepository->wrap($getLastOrderIsNotReview);
        } else {
            return $getLastOrderIsNotReview;
        }
    }

    /**
     * It takes an integer as an argument, finds the row in the database with the same id, and sets the
     * value of the column 'closedDb' to true.
     * 
     * @param int id The id of the row you want to update
     */
    public function makeClosed(int $id)
    {
        $makeClosed = $this->getQuery()->where('id', $id)->first();
        $makeClosed->closedDb = false;
        $makeClosed->save();
    }

    /**
     * It takes an integer as an argument, finds the row in the database with the same id, and sets the
     * value of the column 'closedDb' to true.
     * 
     * @param int id The id of the row you want to update
     */
    public function makeOpen(int $id)
    {
        $makeClosed = $this->getQuery()->where('id', $id)->first();
        $makeClosed->closedDb = true;
        $makeClosed->save();
    }
}
