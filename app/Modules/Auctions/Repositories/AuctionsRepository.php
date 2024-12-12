<?php

namespace App\Modules\Auctions\Repositories;

use App\Modules\Products\Models\Product;
use App\Modules\Customers\Models\Customer;

use App\Modules\Auctions\Models\Auction as Model;
use App\Modules\Auctions\Filters\Auction as Filter;
use App\Modules\Auctions\Resources\Auction as Resource;

use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class AuctionsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'auctions';

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
    const DATA = [
    ];

    /**
     * Auto save uploads in this list
     * If it's an indexed array, in that case the request key will be as database column name
     * If it's associated array, the key will be request key and the value will be the database column name
     *
     * @const array
     */
    const UPLOADS = [];

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
    const INTEGER_DATA = [];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = [
        'price',
    ];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = [];

    /**
     * Geo Location data
     *
     * @const array
     */
    const LOCATION_DATA = [];

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
        'product' => Product::class,
        'customer' => Customer::class,
    ];

    /**
     * Set the columns will be filled with array of records.
     * i.e [tags => TagModel::class]
     *
     * @const array
     */
    const MULTI_DOCUMENTS_DATA = [];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array
     */
    const WHEN_AVAILABLE_DATA = [];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'int' => [
            'product' => 'product.id',
            'customer' => 'customer.id',
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
    const PAGINATE = null;

    /**
     * Number of items per page in pagination
     * If set to null, then it will taken from pagination configurations
     *
     * @const int|null
     */
    const ITEMS_PER_PAGE = null;

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
        // set logged customer
        $model->customer = user()->sharedInfo();
    }

    /**
     * {@inheritdoc}
     */
    public function onCreate($model, $request)
    {
        $this->updateProduct($model, $request);
    }

    /**
     * update product data after add auction
     *
     * @param Model $model
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    private function updateProduct($model, $request)
    {
        $this->productsRepository->getModel($request->product)->update([
            'price' => $model->price,
            'highestBidder' => user()->sharedInfo(),
        ]);
    }

    /**
     * Check if new price is accepted
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    public function checkPrice($request): bool
    {
        $product = Product::where('id', (int) $request->product)->first();

        $modelAuction = Model::where('product.id', (int) $request->product)->orderBy('id', 'desc')->first();

        if (!$modelAuction) {
            return $request->price > $product->auction['price'];
        }

        $lastPriceAdded = $request->price - $modelAuction->price;

        // $lastPriceAdded = $lastPriceAdded + ($lastPriceAdded * 0.1);

        return $lastPriceAdded > $product->auction['minimumPrice'];
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        
    }
}
