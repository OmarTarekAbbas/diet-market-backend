<?php

namespace App\Modules\Categories\Repositories;

use App\Modules\General\Services\Slugging;
use App\Modules\Restaurants\Models\Restaurant;
use App\Modules\Categories\Models\Category as Model;
use App\Modules\Categories\Filters\Category as Filter;
use App\Modules\Categories\Resources\Category as Resource;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class CategoriesRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'categories';

    /**
     * {@inheritDoc}
     */
    const MODEL = Model::class;

    /**
     * {@inheritDoc}
     */
    const RESOURCE = Resource::class;

    /**
     * Set the columns of the data that will be auto filled in the model
     *
     * @const array
     */
    const DATA = ['name', 'description', 'type', 'color', 'metaTag', 'KeyWords'];

    /**
     * Auto save uploads in this list
     * If it's an indexed array, in that case the request key will be as database column name
     * If it's associated array, the key will be request key and the value will be the database column name
     *
     * @const array
     */
    const UPLOADS = ['image'];

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
    const BOOLEAN_DATA = [
        'published',
    ];

    /**
     * Set the columns will be filled with single record of collection data
     * i.e [country => CountryModel::class]
     *
     * @const array
     */
    const DOCUMENT_DATA = [
        // 'restaurant' => Restaurant::class
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
    const WHEN_AVAILABLE_DATA = [
        // 'restaurant',
        'type', 'image', 'description', 'name', 'published', 'color','metaTag', 'KeyWords'
    ];

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
    const PAGINATE = false;

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
     * @param mixed $model
     * @param \Illuminate\Http\Request $request
     * @return  void
     */
    protected function setData($model, $request)
    {
        // if ($request->unitTesting) {
        //     $this->createDataForUnitTest($model, $request);
        //     return;
        // }
        $model->slug = $this->makeSlugForCategory($request, $model);
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
            'id', 'slug', 'name', 'image', 'description', 'color', 'type', 'published',
            // ,'restaurant'
        ];

        return parent::listPublished($options);
    }

    /**
     * {@inheritdoc}
     */
    public function onUpdate($model, $request, $oldModel)
    {
        $this->productsRepository->updateCategoryInfo($model);
        $this->restaurantsRepository->updateCategoryInfo($model);

        $this->modulesRepository->updateCategory($model);
    }

    /**
     * {@inheritdoc}
     */
    public function onDelete($model, $id)
    {
        $this->productsRepository->deleteCategoryInfo($model);
        $this->restaurantsRepository->deleteCategoryInfo($model);
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        if ($type = $this->option('type')) {
            // dd($type);
            $this->query->where('type', $type);
        }

        // $this->query->where('published', true);
    }

    /**
     * Method showItemsForCategoryRepository
     *
     * @param $id $id
     * show Items For Category Repository
     * @return void
     */
    public function showItemsForCategoryRepository($id)
    {
        return $this->itemsRepository->getQuery()->where('categories.id', (int) $id)->get();
    }

    /**
     * Method makeSlugForCategory
     *
     * @param $request $request
     * @param $model $model
     * make Slug For Category
     * @return void
     */
    public function makeSlugForCategory($request, $model)
    {
        $slug = [];
        foreach ($request->name as $name) {
            $slug[] = [
                'text' => $model->getId() . '/' . Slugging::make($name['text'], $name['localeCode']),
                'localeCode' => $name['localeCode'],
            ];
        }

        return $slug;
    }

    // /**
    //  * Method makeDataForUnit
    //  *
    //  * @param $model $model
    //  * @param $request $request
    //  * make Data For Unit
    //  * @return void
    //  */
    // public function makeDataForUnit($model, $request)
    // {
    //     $model->name = $request->name;
    //     $model->description = $request->description;
    //     $model->type = $request->type;
    //     // $model->slug = $this->makeSlugForCategory($request, $model);
    // }

    // /**
    //  * Method createDataForUnitTest
    //  *
    //  * @param $model $model
    //  * @param $request $request
    //  * create Data For Unit Test
    //  * @return void
    //  */
    // public function createDataForUnitTest($model, $request)
    // {
    //     if ($request->method() == 'POST') {
    //         $this->makeDataForUnit($model, $request);
    //     }

    //     if ($request->method() == 'PUT') {
    //         $this->makeDataForUnit($model, $request);
    //     }
    // }

    // public function getCategoryPublished()
    // {
    //     return $this->categoriesRepository->wrapMany($this->categoriesRepository->getQuery()->where('published', true)->get());
    // }
}
