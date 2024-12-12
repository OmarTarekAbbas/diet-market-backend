<?php

namespace App\Modules\Products\Repositories;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Modules\Sku\Models\Sku;
use App\Modules\Units\Models\Unit;
use App\Modules\Brands\Models\Brand;
use App\Modules\General\Helpers\Visitor;
use HZ\Illuminate\Mongez\Helpers\Mongez;
use App\Modules\DietTypes\Models\DietType;
use App\Modules\General\Services\Slugging;
use App\Modules\Categories\Models\Category;
use App\Modules\Options\Models\OptionValue;
use App\Modules\Products\Models\ProductOption;
use App\Modules\Restaurants\Models\Restaurant;
use App\Modules\Products\Models\Product as Model;
use App\Modules\Products\Services\ProductsReports;
use App\Modules\StoreManagers\Models\StoreManager;
use App\Modules\Products\Filters\Product as Filter;
use App\Modules\Products\Models\ProductPackageSize;
use App\Modules\Orders\Repositories\OrdersRepository;
use App\Modules\Products\Resources\Product as Resource;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class ProductsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'products';

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
    const DATA = ['name', 'description', 'discount.type', 'nutritionalValue', 'model', 'typeNutritionalValue', 'specialDietGrams', 'specialDietPercentage', 'type', 'options', 'skuSeller', 'metaTag', 'KeyWords'];

    /**
     * Auto save uploads in this list
     * If it's an indexed array, in that case the request key will be as database column name
     * If it's associated array, the key will be request key and the value will be the database column name
     *
     * @const array
     */
    const UPLOADS = [
        'images',
    ];

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
    const INTEGER_DATA = [
        'rewardPoints', 'purchaseRewardPoints', 'availableStock', 'sales',
        'maxQuantity', 'minQuantity', 'totalRating', 'quantity', 'totalQuantityValues',
    ];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = [
        'discount.value', 'price', 'finalPrice',
        'rating', 'priceInSubscription', 'discount.value', 'width',
    ];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = [
        'published', 'priceIncludesTax', 'hasRequiredOptions', 'inSubscription', 'imported',
    ];

    /**
     * Set columns list of date values.
     *
     * @cont array
     */
    const DATE_DATA = [
        'discount.startDate',
        'discount.endDate',
    ];

    /**
     * Set the columns will be filled with single record of collection data
     * i.e [country => CountryModel::class]
     *
     * @const array
     */
    const DOCUMENT_DATA = [
        'unit' => Unit::class,
        'category' => Category::class,
        'storeManager' => StoreManager::class,
        'dietTypes' => DietType::class,
        'brand' => Brand::class,
        'restaurant' => Restaurant::class,
        'sku' => Sku::class,
        // 'productPackageSize' => ProductPackageSize::class,
    ];

    /**
     * Set the columns will be filled with array of records.
     * i.e [tags => TagModel::class]
     *
     * @const array
     */
    const MULTI_DOCUMENTS_DATA = [
        "relatedProducts" => Model::class,
    ];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array
     */
    const WHEN_AVAILABLE_DATA = [
        'name', 'description', 'discount.type', 'nutritionalValue', 'model', 'discount.startDate', 'discount.endDate',
        'published', 'priceIncludesTax', 'hasRequiredOptions', 'inSubscription', 'imported', 'rewardPoints', 'purchaseRewardPoints',
        'availableStock', 'maxQuantity', 'minQuantity', 'totalRating', 'discount.value', 'price', 'finalPrice', 'rating', 'priceInSubscription',
        'discount.value', 'sales', 'unit', 'category', 'typeNutritionalValue', 'storeManager', 'dietTypes', 'specialDietGrams', 'specialDietPercentage', 'restaurant', 'type', 'options', 'width', 'sku', 'skuSeller', 'metaTag', 'KeyWords'
    ];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'boolean' => ['published'],
        'int' => [
            // 'category' => 'category.id',
            // 'brand' => 'brand.id',
            // 'store' => 'storeManager.id',
            // 'storeManager' => 'storeManager.id',
            // 'storeId' => 'storeManager.store.id',
            // 'restaurant' => 'restaurant.id',
        ],
        'inInt' => [
            'id',
            // 'categories' => 'category.id',
            // 'filters' => 'filters.id',

        ],
        'like' => [
            'name' => 'name.text',
            'description' => 'description.text',
        ],
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
     * If set to true, then the file will be stored as its uploaded name
     *
     * @const bool|null
     */
    const UPLOADS_KEEP_FILE_NAME = false;

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
     * @param mixed $model
     * @param \Illuminate\Http\Request $request
     * @return  void
     */
    protected function setData($model, $request)
    {
        if (!$model['id']) {
            $model->totalViews = 0;
            $model->sales = 0;
        }
        $this->setSlug($model, $request);
        $this->setOptionsInfo($model, $request);
        $this->specialDiet($model, $request);

        if ($model->type == 'products') {
            if ($model->totalQuantityValues > $model->availableStock) {
                throw new Exception('كمية المخزون لا توافق كمية الخيارات');
            }
        }
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
     * {@inheritdoc}
     */
    public function onUpdate($model, $request, $oldModel)
    {
        $this->modulesRepository->updateProduct($model);
        // $this->cartRepository->updateProduct($model);
        $this->favoritesRepository->updateProduct($model);
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        // please move all basic filters into the filter constant
        if ($rating = $this->option('rating')) {
            $this->query->where('rating', (float) $rating);
        }

        if ($except = $this->option('except')) {
            $this->query->whereNotIn('id', array_map('intval', explode(',', $except)));
        }

        // if ($id = $this->option('id')) {
        //     $this->query->whereIn('id', (int) $id);
        // }

        if ($type = $this->option('type')) {
            $this->query->where('type', $type);
        }
        if ($store = $this->option('store')) {
            // dd($store);
            $this->query->where('storeManager.id', $store);
        }
        if ($category = $this->option('category')) {
            // dd($store);
            $this->query->where('category.id', $category);
        }

        if ($restaurant = $this->option('restaurant')) {
            // dd($store);
            $this->query->where('restaurant.id', (int) $restaurant);
        }

        // if ($brand = $this->option('brand')) {
        //     // dd($store);
        //     $this->query->where('brand.id', $brand);
        // }

        if (($priceRange = $this->option('priceRange')) && is_array($this->option('priceRange'))) {
            $this->query->whereBetween('finalPrice', [(float) $priceRange['from'], (float) $priceRange['to']]);
        }

        if ($latest = $this->option('latest')) {
            $method = ($latest == 'latest') ? 'latest' : 'oldest';
            $this->query->{$method}();
        }

        if ($this->option('bestSeller')) {
            $this->query->where('sales', '>', 0)->orderByDesc('sales');
        }

        if ($this->option('alphabetic')) {
            // dd('sdsd');
            $this->query->orderBy('name');
        }

        if ($this->option('specials')) {
            $this->query->whereNotIn('discount.type', ['', null, 'none'])
                ->where('discount.startDate', '<=', Carbon::now())
                ->where('discount.endDate', '>=', Carbon::now());
        }

        if ($this->option('outOfStock')) {
            $this->query->where('availableStock', '<=', 0);
        }

        if ($this->option('totalViews')) {
            $this->query->where('totalViews', '>', 0);
        }

        if (($this->option('inSubscription')) !== null) {
            $this->query->where('inSubscription', true)->where('priceInSubscription', '>', 0);
        }

        if ($options = $this->option('options')) {
            foreach ($options as $option) {
                $this->query->where('options.id', (int) $option['id'])->whereIn('options.values.id', array_map('intval', $option['values']));
            }
        }

        if ($storePublished = $this->option('storePublished')) {
            $this->query->where('storeManager.store.published', $storePublished);
        }

        // $this->query->where('storeManager.published', true);
        $customer = user();
        $guest = Visitor::getDeviceId();
        if ($customer) {
            if (user()->AccountType() == 'customer') {
                $this->checkDietTypes(); //check diet Types
            }
        } elseif ($guest) {
            $this->checkDietTypes(); //check diet Types
        }
        // $this->query->whereNotNull('finalPrice')->whereNotNull('price');
    }

    /**
     * Method checkDietTypes
     *
     * @param $request $request
     * check diet Types
     * @return void
     */
    public function checkDietTypes()
    {
        $customer = user();

        $healthyData = ($customer) ? $this->healthyDatasRepository->getByModel('customerId', $customer->id) : $this->healthyDatasRepository->getByModel('customerDeviceId', Visitor::getDeviceId());

        // dd(Visitor::getDeviceId());
        $healthyDataDiet = $this->dietTypesRepository->get($healthyData->dietTypes);
        if ($healthyDataDiet) {
            $this->queryHealthyDataDiet($healthyDataDiet); //list all prdouct for dietTypes users || Visitor
        } else {
            // dd($healthyData);
            $this->queryHealthyData($healthyData); //list all prdouct for healthyData users users || Visitor
        }
    }

    /**
     * Method queryHealthyDataDiet
     *
     * @param $healthyDataDiet $healthyDataDiet
     *
     * @return void
     */
    public function queryHealthyDataDiet($healthyDataDiet)
    {
        $this->query
            ->whereBetween('specialDietPercentage.fat', [$healthyDataDiet->fatRatio - 15, $healthyDataDiet->fatRatio + 10])
            ->whereBetween('specialDietPercentage.protein', [$healthyDataDiet->proteinRatio - 15, $healthyDataDiet->proteinRatio + 10])
            ->whereBetween('specialDietPercentage.carbs', [$healthyDataDiet->carbohydrateRatio - 15, $healthyDataDiet->carbohydrateRatio + 10]);
    }

    /**
     * Method queryHealthyData
     *
     * @param $healthyData $healthyData
     *
     * @return void
     */
    public function queryHealthyData($healthyData)
    {
        $this->query
            ->whereBetween('specialDietPercentage.fat', [$healthyData->specialDietPercentage['fat'] - 15, $healthyData->specialDietPercentage['fat'] + 10])
            ->whereBetween('specialDietPercentage.protein', [$healthyData->specialDietPercentage['protein'] - 15, $healthyData->specialDietPercentage['protein'] + 10])
            ->whereBetween('specialDietPercentage.carbs', [$healthyData->specialDietPercentage['carbohydrates'] - 15, $healthyData->specialDietPercentage['carbohydrates'] + 10]);
    }

    protected function orderBy(array $orderBy)
    {
        if (($sortRating = $this->option('sortRating')) && in_array($this->option('sortRating'), ['desc', 'asc'])) {
            $this->query->orderBy('rating', $sortRating);
        } elseif (($sortPrice = $this->option('sortPrice')) && in_array($this->option('sortPrice'), ['desc', 'asc'])) {
            $this->query->orderBy('finalPrice', $sortPrice);
        } elseif (($sortAlphabetic = $this->option('sortAlphabetic')) && in_array($this->option('sortAlphabetic'), ['desc', 'asc'])) {
            // todo : refactor this.

            if (Mongez::getRequestLocaleCode() == 'en') {
                $this->query->orderBy('name.0.text', $sortAlphabetic);
            } else {
                $this->query->orderBy('name.1.text', $sortAlphabetic);
            }
        } else {
            parent::orderBy($orderBy);
        }
    }

    /**
     * Get best seller products
     *
     * @return Collection
     */
    public function getBestSeller()
    {
        return $this->listPublished([
            'bestSeller' => true,
            'itemsPerPage' => 15,
        ]);
    }

    /**
     * Get published items
     *
     * @param array $options
     * @return Collection
     */
    public function listPublished(array $options = [])
    {
        $options['select'] = [
            'id', 'slug', 'name', 'images',
            'category', 'discount', 'finalPrice',
            'price', 'imported', 'unit', 'availableStock',
            'minQuantity', 'maxQuantity', 'hasRequiredOptions',
            'inSubscription', 'priceInSubscription', 'options', 'purchaseRewardPoints', 'rewardPoints', 'specialDietGrams', 'specialDietPercentage', 'storeManager', 'brand', 'type', 'restaurant', 'description',
        ];

        if (empty($options['sortAlphabetic'])) {
            $options['sortAlphabetic'] = 'asc';
        }

        return parent::listPublished($options);
    }

    /**
     * Get new arrivals products
     *
     * @return Collection
     */
    public function getNewArrivals()
    {
        return $this->listPublished([
            'latest' => true,
            'itemsPerPage' => 15,
        ]);
    }

    /**
     * Get specials products
     *
     * @return Collection
     */
    public function getSpecials()
    {
        return $this->listPublished([
            'specials' => true,
            'itemsPerPage' => 15,
        ]);
    }

    /**
     * show product with increment views
     *
     * @param $id
     * @return \Illuminate\Http\Resources\Json\JsonResource|\JsonResource|mixed|null
     */
    public function show(int $id)
    {
        $record = $this->getModel($id);

        if (!$record || !$record->published) {
            return null;
        }

        if (!isset($record['totalViews'])) {
            $record['totalViews'] = 0;
            $record->save();
        }

        $record->increment('totalViews');

        return $this->wrap($record);
    }

    /**
     * update sales
     *
     * @param array $productsId
     * @param string $orderStatus
     */
    // public function updateSales(array $productsId, string $orderStatus = OrdersRepository::COMPLETED_STATUS)
    // {
    //     $productIds = Arr::pluck($productsId, 'product.id');

    //     foreach ($productIds as $index => $productId) {
    //         $product = $this->get($productId);
    //         if (!isset($product['sales'])) {
    //             $product['sales'] = 0;
    //             $product->save();
    //         }
    //         // dd($product);
    //         // rollback sales & availableStock if order CANCELED
    //         if (in_array($orderStatus, [OrdersRepository::RETURNED_STATUS, OrdersRepository::REQUEST_RETURNING_ACCEPTED_STATUS, /*OrdersRepository::REFUSED_RECEIVE_STATUS*/])) {
    //             // dd('omar');


    //             $this->update($product->id, [
    //                 'sales' => ($product->sales - 1),
    //                 'availableStock' => ($product->availableStock + $productsId[$index]['quantity']),
    //                 'type' => $product->type,
    //             ]);
    //         } elseif (in_array($orderStatus, [
    //             OrdersRepository::PENDING_STATUS,
    //             // OrdersRepository::COMPLETED_STATUS,
    //             // OrdersRepository::SECOND_WEEK_DELIVERED_STATUS,
    //             // OrdersRepository::THIRD_WEEK_DELIVERED_STATUS,
    //             // OrdersRepository::FOURTH_WEEK_DELIVERED_STATUS,
    //         ])) {
    //             // dd($product);


    //             $this->update($product->id, [
    //                 'sales' => ($product->sales + 1),
    //                 'availableStock' => ($product->availableStock - $productsId[$index]['quantity']),
    //                 'type' => $product->type,
    //             ]);
    //         }
    //     }
    // }

    /**
     * Method updateSaleReturning
     *
     * @param $orderedProducts $orderedProducts
     * @param string $orderStatus
     *
     * @return void
     */
    public function updateSaleReturning($orderedProducts)
    {
        $productId = $orderedProducts['product']['id'];
        $product = $this->getModel($productId);

        if (!isset($product['sales'])) {
            $product['sales'] = 0;
            $product->save();
        }

        // rollback sales & availableStock if order CANCELED

        // update option quantity
        $updatedOptions = [];
        foreach ($product['options'] as $option) {
            foreach ($orderedProducts['options'] as $key => $orderProductOption) {
                if ($option['id'] == $orderProductOption['id'] && isset($orderProductOption['values'][$key]['subtractFromStock'])) {
                    if ($orderProductOption['values'][$key]['subtractFromStock']) {
                        foreach ($option['values'] as $valueIndex => $optionValue) {
                            if ($optionValue['id'] == $orderProductOption['values'][0]['id']) {
                                $option['values'][$valueIndex]['quantity'] = $optionValue['quantity'] + 1;
                            }
                        }
                    }
                }
            }
            $updatedOptions[] = $option;
        }
        // dd($product->availableStock);
        $product->options = $updatedOptions;
        //                $product->sales = $product->sales - 1;
        $product->sales = $product->sales - $orderedProducts['quantity'];
        $product->availableStock = $product->availableStock + $orderedProducts['quantity'];
        $product->type = $product->type;
        $product->save();
    }

    /**
     * Method updateSales
     *
     * @param array $productsId
     * @param string $orderStatus
     *
     * @return void
     */
    public function updateSales(array $productsId, string $orderStatus = OrdersRepository::COMPLETED_STATUS)
    {
        //        $productIds = Arr::pluck($productsId, 'product.id');
        $productIds = $productsId;

        foreach ($productIds as $index => $orderedProducts) {
            $productId = $orderedProducts['product']['id'];
            $product = $this->getModel($productId);

            if (!isset($product['sales'])) {
                $product['sales'] = 0;
                $product->save();
            }


            // rollback sales & availableStock if order CANCELED
            if (in_array($orderStatus, [OrdersRepository::RETURNED_STATUS, OrdersRepository::WALLET_PRODUCT]/*, OrdersRepository::REFUSED_RECEIVE_STATUS]*/)) {
                // // update option quantity
                // $updatedOptions = [];
                // foreach ($product['options'] as $option) {
                //     foreach ($orderedProducts['options'] as $key =>  $orderProductOption) {
                //         if ($option['id'] == $orderProductOption['id'] && isset($orderProductOption['values'][$key]['subtractFromStock'])) {
                //             if ($orderProductOption['values'][$key]['subtractFromStock']) {
                //                 foreach ($option['values'] as $valueIndex => $optionValue) {
                //                     if ($optionValue['id'] == $orderProductOption['values'][0]['id']) {
                //                         $option['values'][$valueIndex]['quantity'] = $optionValue['quantity'] + 1;
                //                     }
                //                 }
                //             }
                //         }
                //     }
                //     $updatedOptions[] = $option;
                // }
                // // dd($product->availableStock);
                // $product->options = $updatedOptions;
                // //                $product->sales = $product->sales - 1;
                // $product->sales = $product->sales - $productsId[$index]['quantity'];
                // $product->availableStock = $product->availableStock + $productsId[$index]['quantity'];
                // $product->type =  $product->type;
            } elseif (in_array($orderStatus, [OrdersRepository::PENDING_STATUS])) {
                // update option quantity
                $updatedOptions = [];

                // if($product['options'] == null){
                //     continue;
                // }

                foreach ($product['options'] as $option) {
                    foreach ($orderedProducts['options'] as $key => $orderProductOption) {
                        if ($option['id'] == $orderProductOption['id'] && isset($orderProductOption['values'][$key]['subtractFromStock'])) {
                            if ($orderProductOption['values'][$key]['subtractFromStock']) {
                                foreach ($option['values'] as $valueIndex => $optionValue) {
                                    if ($optionValue['id'] == $orderProductOption['values'][0]['id']) {
                                        $option['values'][$valueIndex]['quantity'] = $optionValue['quantity'] - 1;
                                    }
                                }
                            }
                        }
                    }
                    $updatedOptions[] = $option;
                }
                $product->options = $updatedOptions;
                $product->sales = $product->sales + $productsId[$index]['quantity'];
                $product->availableStock = $product->availableStock - $productsId[$index]['quantity'];
                $product->type = $product->type;
                // dd($product);

                // dd($product->options ,$product->sales, $product->availableStock , $product->type ,$productsId[$index]['quantity'] );
            }
            $product->save();
        }
    }

    /**
     * Update category info on category update
     *
     * @param Category $category
     * @return void
     */
    public function updateCategoryInfo($category)
    {
        Model::where('category.id', $category->id)->update([
            'category' => $category->sharedInfo(),
        ]);
    }

    /**
     * Update category info on category update
     *
     * @param Category $category
     * @return void
     */
    public function updateOptionInfo($option)
    {
        $updateOptionInfos = Model::where('options.option.id', $option->id)->get();
        // dd($updateOptionInfos);
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
        Model::where('category.id', $category->id)->delete();
    }

    /**
     * Get total products for the given options
     *
     * @param array $options
     * @return int
     */
    public function getTotal(array $options): int
    {
        $query = $this->getQuery();

        if (isset($options['published'])) {
            $query->where('published', $options['published']);
        }

        if (isset($options['category'])) {
            $query->where('category.id', $options['category']);
        }

        return $query->count();
    }

    private function setOptionsInfo($model, $request)
    {
        $options = (array) $request->options ?: [];

        $productOptions = [];

        $hasRequiredOptions = false;

        foreach ($options as $option) {
            // dd($option);

            $optionInfo = $this->optionsRepository->getModel($option['optionId']);

            if (!$optionInfo) {
                continue;
            }
            $data = [
                'option' => $optionInfo->pluck(['id', 'name', 'isMultiSelection', 'typeProduct']),
                'subtractFromStock' => (bool) ($option['subtractFromStock'] ?? false),
                'required' => $isRequiredOption = (bool) ($option['required'] ?? false),
                'values' => $this->mapOptionValues($option['values']),
            ];

            if (!empty($option['id'])) {
                $productOption = ProductOption::find($option['id']);
                if ($productOption) {
                    $productOption->update($data);
                } else {
                    $productOption = ProductOption::create($data);
                }
            } else {
                $productOption = ProductOption::create($data);
            }

            $productOptions[] = $productOption->sharedInfo();

            if ($isRequiredOption === true) {
                $hasRequiredOptions = true;
            }
            if (request()->type == 'products') {
                foreach ($option['values'] as $key => $value) {
                    $totalQuantityValues = 0;
                    $totalQuantityValues = $model->totalQuantityValues + $value['quantity'] ?? 0;
                    $model->totalQuantityValues = $totalQuantityValues;
                }
            }
        }

        $model->hasRequiredOptions = $hasRequiredOptions;
        $model->options = $productOptions;
    }

    /**
     * Get the names and cast the price for each value
     *
     * @param array $optionValues
     * @return array
     */
    private function mapOptionValues(array $optionValues): array
    {
        $values = [];

        foreach ($optionValues as $value) {
            $optionValue = OptionValue::find($value['id']);

            if (!$optionValue) {
                continue;
            }

            $optionValue = $optionValue->sharedInfo();

            unset($optionValue['defaultPrice']);

            $optionValue['price'] = (float) ($value['price'] ?? 0);
            $optionValue['quantity'] = (int) ($value['quantity'] ?? 0);
            $optionValue['subtractFromStock'] = (bool) ($value['subtractFromStock'] ?? 0);

            $values[] = $optionValue;
        }

        return $values;
    }

    /**
     * Get Products Reports Generator
     *
     * @return ProductsReports
     */
    public function reports(): ProductsReports
    {
        return new ProductsReports($this);
    }

    /**
     * {@inheritDoc}
     */
    public function onSave($model, $request, $oldModel = null)
    {
        if ($model->discount['type'] == 'none') {
            $model->finalPrice = $model->price;
        }
    }

    /**
     * @param $model
     * @param $id
     * {@inheritDoc}
     */
    public function onDelete($model, $id)
    {
        $this->modulesRepository->removeProduct($model);
        $this->favoritesRepository->removeProduct($model);
    }

    /**
     * update Rate
     *
     * @param int $productId
     * @param float $avg
     * @param int $total
     */
    public function updateRate(int $productId, float $avg, int $total)
    {
        $product = $this->getModel($productId);
        $product->update([
            'rating' => $avg,
            'totalRating' => $total,
        ]);
    }

    public function getExpiredDiscount()
    {
        return $this->getQuery()
            //            ->whereRaw('price != finalPrice')
            ->where('discount.endDate', '<=', Carbon::now())
            ->orwhereIn('discount.type', ['', null, 'none'])
            ->orwhereIn('finalPrice', [0, null])
            ->get();
    }

    /**
     * Update Store Manager Info
     *
     * @param StoreManager $manager
     */
    public function updateStoreManager($storeManger)
    {
        Model::where('storeManager.id', $storeManger['id'])->update([
            'storeManager' => $storeManger->sharedInfo(),
        ]);
    }

    /**
     * Delete Store Manager Products Once He is Deleted
     *
     * @param int $managerId
     */
    public function deleteManagerProducts(int $managerId)
    {
        Model::where('storeManager.id', $managerId)->delete();
    }

    /**
     * Method specialDiet
     *
     * @param $model $model
     * @param $request $request
     * MAKE special Diet
     * @return void
     */
    public function specialDiet($model, $request)
    {
        $this->specialDietPercentageAndGrams($model, $request);
    }


    /**
     * It takes three numbers as arguments and returns the sum of the product of each number multiplied
     * by its corresponding multiplier
     * 
     * @param fat the amount of fat in grams
     * @param protein 4
     * @param carbohydrates the amount of carbohydrates in grams
     * 
     * @return float
     */
    public function calculatorCalories($fat, $protein, $carbohydrates): float
    {
        return ($fat * 9) + ($protein * 4) + ($carbohydrates * 4);
    }

    /**
     * Method specialDietPercentageAndGrams
     *
     * @param $model $model
     * @param $request $request
     *special Diet Percentage And Grams
     * @return void
     */
    public function specialDietPercentageAndGrams($model, $request)
    {
        if ($request->nutritionalValue) {
            if ($request->typeNutritionalValue == 'grams') {
                $calories = $this->calculatorCalories($request->nutritionalValue['fat'], $request->nutritionalValue['protein'], $request->nutritionalValue['carbs']);

                $model->specialDietGrams = [
                    'fat' => round($request->nutritionalValue['fat']),
                    'protein' => round($request->nutritionalValue['protein']),
                    'carbs' => round($request->nutritionalValue['carbs']),
                    'calories' => round($calories),
                ];


                $model->specialDietPercentage = [
                    'fat' => round((($request->nutritionalValue['fat'] * 9) / $calories * 100)),
                    'protein' => round((($request->nutritionalValue['protein'] * 4) / $calories * 100)),
                    'carbs' => round((($request->nutritionalValue['carbs'] * 4) / $calories * 100)),
                    'calories' => round($calories),
                ];
            }
        }
    }

    /**
     * Method saveBranch
     *
     * @param BranchesClub $branchesClub
     *
     * @return void
     */
    public function saveProducts(Brand $brand)
    {
        $product = Model::where('brand.id', $brand['id'])->update([
            'brand' => $brand->sharedInfo(),
        ]);
    }

    /**
     * Method relatedProducts
     *
     * @param $categoryId $categoryId
     * @param $id $id
     *
     * @return void
     */
    public function relatedProducts($categoryId, $id)
    {
        return $this->productsRepository->wrapMany($this->productsRepository->getQuery()->where('category.id', (int) $categoryId)->where('id', '!=', (int) $id)->where('type', 'products')->get());
    }

    /**
     * update product option if option was updated
     * @param Option $option
     * @return void
     */
    public function updateProductOption($option)
    {
        $productOptions = ProductOption::where('option.id', $option->id)->get();
        foreach ($productOptions as $productOption) {
            unset($option->optionValues);
            $productOption->option = $option->sharedInfo();
            $productOption->save();
            Model::where('options.id', $productOption->id)->update([
                'options.$' => $productOption->sharedInfo(),
            ]);
        }
    }

    /**
     * Delete product option if option was deleted
     * @param int $optionId
     * @return void
     */
    public function deleteProductOption(int $optionId)
    {
        $productOptions = ProductOption::where('option.id', $optionId)->get();
        foreach ($productOptions as $productOption) {
            Model::where('options.id', $productOption->id)->update([
                'options' => null,
            ]);
        }
        ProductOption::where('option.id', $optionId)->delete();
    }

    /**
     * Method isAvailableItem
     *
     * @param $id $id
     *
     * @return void
     */
    public function isAvailableItem($id)
    {
        $isAvailableItem = $this->productsRepository->get($id);
        // dd($isAvailableItem , $isAvailableItem->availableStock , $isAvailableItem->published ,request()->type);
        if (request()->type == 'products') {
            if ($isAvailableItem && $isAvailableItem->availableStock > 0 && $isAvailableItem->published == true) {
                return true;
            }

            return false;
        } else {
            if ($isAvailableItem && $isAvailableItem->published == true) {
                return true;
            }

            return false;
        }
    }
}
