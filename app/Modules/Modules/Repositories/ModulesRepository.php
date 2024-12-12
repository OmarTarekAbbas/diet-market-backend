<?php

namespace App\Modules\Modules\Repositories;

use App\Modules\Banners\Models\Banner;
use App\Modules\Sliders\Models\Slider;
use App\Modules\Products\Models\Product;
use App\Modules\Categories\Models\Category;

use App\Modules\Modules\Models\Module as Model;
use App\Modules\Modules\Filters\Module as Filter;
use App\Modules\Modules\Resources\Module as Resource;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class ModulesRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'modules';

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
    const DATA = ['name', 'title', 'type', 'value', 'app', 'layout'];

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
    const ARRAYBLE_DATA = ['extra'];

    /**
     * Set columns list of integers values.
     *
     * @cont array
     */
    const INTEGER_DATA = ['sortOrder'];

    /**
     * Set the default order by for the repository
     *
     * @const array
     */
    const ORDER_BY = ['sortOrder', 'ASC'];

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
    const DOCUMENT_DATA = [];

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
        'boolean' => ['published'],
        '=' => [
            'app',
            'layout',
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
     * @param mixed $model
     * @param \Illuminate\Http\Request $request
     * @return  void
     */
    protected function setData($model, $request)
    {
        // slider
        // product
        // category
        // banner
        // twoBanners
        // products collection
        if (is_numeric($model->value)) {
            $model->value = (int) $model->value;
        } elseif (is_array($model->value) && is_numeric($model->value[0])) {
            $model->value = array_map('intval', (array) $model->value);
        }

        switch ($model->type) {
            case 'bestSeller':
            case 'newArrivals':
            case 'specials':
                break;
            case 'banner':
                $model->data = [
                    'banner' => $this->bannersRepository->sharedInfo($model->value),
                ];

                break;
            case 'twoBanners':
                $model->data = [
                    'banners' => array_slice($this->listSharedInfo('banners', $model->value), 0, 2),
                ];

                break;
            case 'slider':
                $model->data = [
                    'slider' => $this->slidersRepository->sharedInfo($model->value),
                ];

                break;
            case 'productsCollection':
                $model->data = [
                    'products' => $this->listSharedInfo('products', $model->value),
                ];

                break;
            case 'categoryProducts':
                $extraProductsIds = array_map('intval', $model->extra['products']);

                $model->extra = [
                    'products' => $extraProductsIds,
                ];

                $model->data = [
                    'products' => $this->listSharedInfo('products', $extraProductsIds),
                    'category' => $this->categoriesRepository->sharedInfo($model->value),
                ];

                break;
            case 'featuredCategories':
                $model->data = [
                    'categories' => $this->listSharedInfo('categories', $model->value),
                ];

                break;
        }
    }

    /**
     * Get list of data with its shared info only
     *
     * @param string $repository
     * @param array $ids
     * @return array
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    protected function listSharedInfo(string $repository, $ids): array
    {
        return repo($repository)->list([
            'as-model' => true,
            'id' => $ids,
            'published' => true,
            'paginate' => false,
            'sortAlphabetic' => 'asc',
        ])->map(function ($item) {
            return $item->sharedInfo();
        })->toArray();
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
            'id', 'data', 'type', 'title',
        ];

        $options['as-model'] = true;

        $modules = parent::listPublished($options);

        foreach ($modules as $moduleIndex => $module) {
            // products
            if (!empty($module->data['products'])) {
                $products = $module->data['products'];

                foreach ($products as $index => $product) {
                    if ($product['published'] === true) {
                        continue;
                    }

                    unset($products[$index]);
                }

                if (empty($products)) {
                    unset($modules[$moduleIndex]);
                }

                $data = $module->data;
                $data['products'] = $products;
                $module->data = $data;
            }

            // category
            if (!empty($module->data['category'])) {
                $category = $module->data['category'];

                if ($category['published'] === true) {
                    continue;
                }

                unset($modules[$moduleIndex]);
            }

            // categories
            if (!empty($module->data['categories'])) {
                $categories = $module->data['categories'];

                foreach ($categories as $index => $category) {
                    if ($category['published'] === true) {
                        continue;
                    }

                    unset($categories[$index]);
                }

                if (empty($categories)) {
                    unset($modules[$moduleIndex]);
                }

                $data = $module->data;
                $data['categories'] = $categories;
                $module->data = $data;
            }

            // banners
            if (!empty($module->data['banners'])) {
                $banners = $module->data['banners'];

                foreach ($banners as $index => $banner) {
                    if ($banner['published'] === true) {
                        continue;
                    }

                    unset($banners[$index]);
                }

                if (empty($banners)) {
                    unset($modules[$moduleIndex]);
                }

                $data = $module->data;
                $data['banners'] = $banners;
                $module->data = $data;
            }

            // banner
            if (!empty($module->data['banner'])) {
                $banner = $module->data['banner'];

                if ($banner['published'] === true) {
                    continue;
                }

                unset($modules[$moduleIndex]);
            }

            // slider
            if (!empty($module->data['slider'])) {
                $slider = $module->data['slider'];

                if ($slider['published'] === false) {
                    unset($modules[$moduleIndex]);

                    continue;
                }

                foreach ($slider['banners'] as $bannerIndex => $banner) {
                    if ($banner['published'] === true) {
                        continue;
                    }

                    unset($slider['banners'][$bannerIndex]);
                }

                if (empty($slider['banners'])) {
                    unset($modules[$moduleIndex]);
                }

                $data = $module->data;
                $data['slider'] = $slider;
                $module->data = $data;
            }
        }

        return $this->wrapMany($modules);
    }

    /**
     * Update slider info when slider gets updated
     *
     * @param Slider $slider
     * @return void
     */
    public function updateSlider(Slider $slider)
    {
        $sliderInfo = $slider->sharedInfo();

        Model::where('data.slider.id', $slider->id)->update([
            'data.slider' => $sliderInfo,
            'published' => $slider->published,
        ]);
    }

    /**
     * Update banners info when banner gets updated
     *
     * @param Banner $banner
     * @return void
     */
    public function updateBanner(Banner $banner)
    {
        $bannerInfo = $banner->sharedInfo();

        Model::where('data.banner.id', $banner->id)->update([
            'data.banner' => $bannerInfo,
            'published' => $banner->published,
        ]);

        Model::where('data.banners.id', $banner->id)->update([
            'data.banners.$' => $bannerInfo,
        ]);
    }

    /**
     * Delete module banner is removed
     *
     * @param Banner $banner
     * @return void
     */
    public function removeBanner(Banner $banner)
    {
        Model::where('data.banner.id', $banner->id)->orWhere('data.banners.id', $banner->id)->delete();
    }

    /**
     * Delete slider module when slider is removed
     *
     * @param Slider $slider
     * @return void
     */
    public function removeSlider(Slider $slider)
    {
        Model::where('data.slider.id', $slider->id)->delete();
    }

    /**
     * Update products in modules
     *
     * @param Product $product
     * @return void
     */
    public function updateProduct(Product $product)
    {
        $info = $product->sharedInfo();

        Model::where('data.products.id', $product->id)->update([
            'data.products.$' => $info,
        ]);
    }

    /**
     * Update products in modules
     *
     * @param Category $category
     * @return void
     */
    public function updateCategory(Category $category)
    {
        $info = $category->sharedInfo();

        Model::where('data.category.id', $category->id)->update([
            'data.category' => $info,
            'published' => $category->published,
        ]);

        Model::where('data.categories.id', $category->id)->update([
            'data.categories.$' => $info,
        ]);
    }

    /**
     * Delete module category when category is removed
     *
     * @param Category $category
     * @return void
     */
    public function removeCategory(Category $category)
    {
        Model::where('data.category.id', $category->id)->orWhere('data.categories.id', $category->id)->delete();
    }

    /**
     * Delete product from the module when product is removed
     *
     * @param Product $product
     * @return void
     */
    public function removeProduct(Product $product)
    {
        Model::where('data.products.id', $product->id)->pull('data.products', [
            'id' => $product->id,
        ]);

        // make sure to clear all modules that are productsCollection or categoryProducts
        // if the data products array is empty
        Model::whereIn('type', ['productsCollection', 'categoryProducts'])->where('data.products', 'size', 0)->orWhereNull('data.products')->delete();
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        // if ($id = $this->option('id')) {
        //     $this->query->where('id', (int) $id);
        // }

        // $this->query->orderBy('sortOrder');
    }
}
