<?php

namespace App\Modules\Favorites\Repositories;

use Illuminate\Http\Request;
use App\Modules\Products\Models\Product;
use App\Modules\Favorites\Models\Favorite as Model;
use App\Modules\Favorites\Filters\Favorite as Filter;
use App\Modules\Favorites\Resources\Favorite as Resource;

use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class FavoritesRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'favorites';

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
    const DATA = [];

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
    const FLOAT_DATA = [];

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
    const FILTER_BY = [];

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
     * @param mixed $model
     * @param \Illuminate\Http\Request $request
     * @return  void
     */
    protected function setData($model, $request)
    {
        if (!$model['id']) {
            $model->customerId = user()->id;
        }
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        if ($customer = $this->option('customer')) {
            $this->query->where('customerId', (int) $customer);
        }

        $this->query->where('product.published', true);
    }

    /**
     * add product to favorites
     *
     * @param Request $request
     */
    public function addToFavorites(Request $request)
    {
        $this->create([
            'product' => (int) $request->productId,
            'customerId' => user()->id,
        ]);
    }

    /**
     * @param Request $request
     */
    public function removeFromFavorites(Request $request)
    {
        $this->getQuery()->where('product.id', (int) $request->productId)->where('customerId', user()->id)->delete();
    }

    /**
     * @param int $productId
     * @return mixed
     */
    public function existsInFavorites($productId)
    {
        return $this->getQuery()->where('product.id', $productId)->where('customerId', user()->id)->exists();
    }

    // todo : save total in customer
    /**
     * {@inheritdoc}
     */
    public function onSave($model, $request, $oldModel = null)
    {
        $this->customersRepository->updateFavoritesCount($model->customerId);
    }

    /**
     * Remove product from wishlist
     *
     * @param  Product $product
     * @return void
     */
    public function removeProduct($product)
    {
        $favorites = $this->getQuery()->where('product.id', $product->id)->get();

        foreach ($favorites as $favorite) {
            $this->delete($favorite->id);
        }
    }

    /**
     * Method updateProduct
     *
     * @param $product $product
     *
     * @return void
     */
    public function updateProduct($product)
    {
        $info = $product->sharedInfo();

        Model::where('product.id', $product->id)->update([
            'product' => $info,
        ]);
    }
}
