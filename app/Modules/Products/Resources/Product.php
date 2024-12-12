<?php

namespace App\Modules\Products\Resources;

use Carbon\Carbon;
use App\Modules\Sku\Resources\Sku;
use App\Modules\Units\Resources\Unit;
use App\Modules\Brands\Resources\Brand;
use App\Modules\DietTypes\Resources\DietType;
use App\Modules\Categories\Resources\Category;
use App\Modules\Restaurants\Resources\Restaurant;
use App\Modules\StoreManagers\Resources\StoreManager;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class Product extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['specialDietGrams', 'specialDietPercentage', 'type', 'skuSeller', 'metaTag', 'KeyWords'];

    /**
     * String Data
     *
     * @const array
     */
    const STRING_DATA = ['discount.type'];

    /**
     * Boolean Data
     *
     * @const array
     */
    const BOOLEAN_DATA = ['published', 'priceIncludesTax', 'hasRequiredOptions', 'imported', 'inSubscription', 'isRated'];

    /**
     * Integer Data
     *
     * @const array
     */
    const INTEGER_DATA = [
        'id', 'rewardPoints', 'purchaseRewardPoints', 'availableStock', 'maxQuantity', 'minQuantity', 'totalViews', 'sales',
        'totalRating', 'priceInSubscription', 'quantity', 'totalQuantityValues',
    ];

    /**
     * Object Data
     *
     * @const array
     */
    const OBJECT_DATA = ['nutritionalValue'];

    /**
     * Float Data
     *
     * @const array
     */
    const FLOAT_DATA = ['price', 'finalPrice', 'rating', 'discount.value', 'priceInSubscription', 'width'];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = [
        'published', 'priceInSubscription', 'priceIncludesTax', 'hasRequiredOptions', 'imported', 'inSubscription', 'availableStock',
        'minQuantity', 'totalViews', 'sales', 'images', 'name', 'description', 'nutritionalValue', 'model', 'slug', 'discount', 'discount.startDate',
        'discount.endDate', 'options', 'discount.type', 'discount.value', 'isRated', 'rating', 'typeNutritionalValue', 'restaurant', 'type', 'storeManager', 'brand', 'specialDietGrams', 'specialDietPercentage', 'unit', 'width', 'metaTag', 'KeyWords'
    ];

    /**
     * Set that columns that will be formatted as dates
     * it could be numeric array or associated array to set the date format for certain columns
     *
     * @const array
     */
    const DATES = [
        'discount.startDate',
        'discount.endDate',
    ];

    /**
     * Data that has multiple values based on locale codes
     * Mostly this is used with mongodb driver
     *
     * @const array
     */
    const LOCALIZED = [
        'name', 'description', 'model', 'slug',
    ];

    /**
     * List of assets that will have a full url if available
     */
    const ASSETS = [
        'images',
    ];

    /**
     * Data that will be returned as a resources
     *
     * i.e [city => CityResource::class],
     * @const array
     */
    const RESOURCES = [
        'unit' => Unit::class,
        'category' => Category::class,
        'storeManager' => StoreManager::class,
        // 'dietTypes' => DietType::class,
        'brand' => Brand::class,
        'restaurant' => Restaurant::class,
        'sku' => Sku::class,
        // 'productPackageSize' => ProductPackageSize::class,
    ];

    /**
     * Data that will be returned as a collection of resources
     *
     * i.e [cities => CityResource::class],
     * @const array
     */
    const COLLECTABLE = [
        'options' => ProductOption::class,
        'relatedProducts' => Product::class,
    ];

    /**
     * List of keys that will be unset before sending
     *
     * @var array
     */
    protected static $disabledKeys = [];

    /**
     * List of keys that will be taken only
     *
     * @var array
     */
    protected static $allowedKeys = [];

    /**
     * @return array|void
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    protected function extend($request)
    {
        $this->checkDiscount();
        $this->setProductUrl();
        $this->checkMaxQuantity();
        $this->checkIfProductIsInFavorite();
        $this->getInCart();
        $this->set('priceInSubscriptionText', trans('products.price', ['value' => $this->priceInSubscription]));


        $this->set('priceText', trans('products.price', ['value' => $this->price]));

        if ($this->sku) {
            $this->set('skuAll', $this->sku['name'] . $this->skuSeller);
        }

        $this->set('rating', round($this->rating, 1));
    }

    /**
     * Set product url
     *
     * @return void
     */
    private function setProductUrl()
    {
        if (empty($this->slug) || !is_string($this->data['slug'])) {
            return;
        }

        $this->set('url', config('app.ui_url') . '/products/' . $this->data['slug']);
    }

    /**
     * Check product discount
     *
     * @return void
     */
    private function checkDiscount()
    {
        $hasDiscount = false;

        if ($this->discount && isset($this->discount['startDate']) && isset($this->discount['endDate']) && !empty($this->discount['type']) && !in_array($this->discount['type'], ['', null, 'none'])) {
            if (
                !(is_array($this->discount['startDate']) || is_array($this->discount['endDate'])) &&
                Carbon::now()->between(Carbon::createFromTimestampMs($this->discount['startDate']), Carbon::createFromTimestampMs($this->discount['endDate']))
            ) {
                $hasDiscount = true;
            }
        }

        if ($hasDiscount) {
            if ($this->discount['type'] == 'percentage') {
                $this->set('discount.valueText', "{$this->discount['value']} %");
            } elseif ($this->discount['type'] == 'amount') {
                $this->set('discount.valueText', trans('products.price', ['value' => $this->discount['value']]));
            }

            $this->set('finalPriceText', trans('products.price', ['value' => $this->finalPrice]));
        } else {
            if ($this->price != $this->finalPrice) {
                $this->set('finalPrice', $this->price);
                $this->set('finalPriceText', trans('products.price', ['value' => $this->price]));
            } else {
                $this->set('finalPriceText', trans('products.price', ['value' => $this->finalPrice]));
            }
        }

        $this->set('hasDiscount', $hasDiscount);
    }

    /**
     * Check if product has no max quantity, if so, then set max quantity to -1
     *
     * @return void
     */
    private function checkMaxQuantity()
    {
        $hasMaxQuantity = true;

        if (!user() || user()->accountType() === 'customer') {
            if ($this->maxQuantity == 0) {
                $this->set('maxQuantity', 999);
                $hasMaxQuantity = false;
            }
        }

        $this->set('hasMaxQuantity', $hasMaxQuantity);
    }

    /**
     * Check if product is in favorite
     *
     * @return void
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    private function checkIfProductIsInFavorite()
    {
        $isFavorite = false;

        $customer = user();

        if ($customer && $customer->accountType() === 'customer') {
            // dd($this->id);
            $isFavorite = repo('favorites')->existsInFavorites($this->id);
        }

        $this->set('isFavorite', $isFavorite);
    }

    public function getInCart()
    {
        $cart = repo('cart')->getCurrentCart(null, false);
        $subscriptionCart = repo('cart')->getCurrentCart(null, true);

        $inCart = false;
        $inSubscriptionCart = false;

        $quantityInCart = 0;
        $quantityInSubscriptionCart = 0;

        $itemId = null;
        $itemIdSubscription = null;

        $itemOptions = null;
        $itemSubscriptionOptions = null;

        if ($item = collect($cart['items'])->where('product.id', $this->id)->first()) {
            $inCart = true;
            $quantityInCart = $item['quantity'];
            $itemId = $item['id'];
            $itemOptions = ProductOption::collection($item['options']);
        }

        if ($item = collect($subscriptionCart['items'])->where('product.id', $this->id)->first()) {
            $inSubscriptionCart = true;
            $quantityInSubscriptionCart = $item['quantity'];
            $itemIdSubscription = $item['id'];
            $itemOptions = ProductOption::collection($item['options']);
        }

        $this->set('cart', [
            'inCart' => $inCart,
            'quantityInCart' => $quantityInCart,
            'itemId' => $itemId,
            'options' => $itemOptions,
        ]);

        $this->set('subscriptionCart', [
            'inCart' => $inSubscriptionCart,
            'quantityInCart' => $quantityInSubscriptionCart,
            'itemId' => $itemIdSubscription,
            'options' => $itemSubscriptionOptions,
        ]);
    }
}
