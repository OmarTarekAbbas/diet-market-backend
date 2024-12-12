<?php

namespace App\Modules\Options\Repositories;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use App\Modules\Options\Models\OptionValue;
use App\Modules\Options\Models\Option as Model;
use App\Modules\Options\Filters\Option as Filter;
use App\Modules\Options\Resources\Option as Resource;

use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class OptionsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'options';

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
    const DATA = ['name', 'type', 'typeProduct'];

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
    const BOOLEAN_DATA = ['isMultiSelection'];

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
    const WHEN_AVAILABLE_DATA = ['name', 'type', 'values', 'status', 'typeProduct', 'isMultiSelection'];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'boolean' => ['published'],
        'like' => [
            'name' => 'name.text',
            'typeProduct' => 'typeProduct',
        ],
        'int' => ['id'],
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
        if (!empty($request->values)) {
            $values = [];

            foreach ($request->values as $value) {
                if (!empty($value['id'])) {
                    // we're updating
                    $valueModel = OptionValue::find($value['id']);
                } else {
                    //we are creating
                    $valueModel = new OptionValue();
                }

                $valueModel->name = (array) $value['name'];
                $valueModel->sortOrder = (int) $value['sortOrder'];
                $valueModel->image = $this->uploadImage($value['image'] ?? $valueModel->image);

                $valueModel->save(); // if creating a new id will be generated

                $values[] = $valueModel->sharedInfo(); // [id, name]
            }

            $model->values = $values;
        }
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        
    }

    /**
     * upload image from values
     *
     * @param $image
     * @return string
     */
    public function uploadImage($image): ?string
    {
        if ($image instanceof UploadedFile) {
            $destinationPath = $this->getUploadsStorageDirectoryName();
            $imageName = $destinationPath . '/' . date('YmdHis') . '-' . Str::random(10) . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $imageName);

            return $imageName;
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function onDelete($model, $id)
    {
        // $this->optionValuesRepository->deleteOptionValues($id);
        $this->productsRepository->deleteProductOption((int) $id);
    }

    /**
     * {@inheritdoc}
     */
    public function onUpdate($model, $request, $oldModel)
    {
        $this->productsRepository->updateProductOption($model);
    }
}
