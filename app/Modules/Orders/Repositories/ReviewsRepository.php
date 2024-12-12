<?php

namespace App\Modules\Orders\Repositories;

use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\Review as Model;
use App\Modules\Orders\Filters\Review as Filter;
use App\Modules\Orders\Resources\Review as Resource;

use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class ReviewsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'reviews';

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
    const DATA = ['review', 'status'];

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
    const INTEGER_DATA = [
        'orderId',
        'storeId',
        'orderQuality',
        'deliverySpeed',
    ];

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
    const WHEN_AVAILABLE_DATA = [
        'review',
        'orderId',
        'storeId',
        'orderQuality',
        'deliverySpeed',
        'status',
    ];

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
    const PAGINATE = null;

    /**
     * Number of items per page in pagination
     * If set to null, then it will taken from pagination configurations
     *
     * @const int|null
     */
    const ITEMS_PER_PAGE = null;

    /**
     * review status
     */
    const PENDING_STATUS = 'pending';

    const ACCEPTED_STATUS = 'accepted';

    const REJECTED_STATUS = 'rejected';

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
            $model->status = static::PENDING_STATUS;
            $model->customer = user()->sharedInfo();
            $model->rate = ($request->orderQuality + $request->deliverySpeed) / 2;
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
            $this->query->where('customer.id', (int) $customer);
        }

        if ($order = $this->option('order')) {
            $this->query->where('orderId', (int) $order);
        }

        if ($status = $this->option('status')) {
            $this->query->where('status', $status);
        }
        if ($id = $this->option('id')) {
            $this->query->where('id', (int) $id);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function onCreate($model, $request)
    {
        $order = $this->ordersRepository->getModel((int) $model->orderId);

        if (!$this->ordersRepository->isRatedBefore($order)) {
            $this->ordersRepository->rate($order, $model->only('orderQuality', 'deliverySpeed', 'review', 'rate'));
        }
    }

    /**
     * @param Order $order
     * @param $request
     */
    public function rate(Order $order, $request)
    {
        // dd($request->all());
        $this->create([
            'orderId' => $order->id,
            'orderQuality' => $request->orderQuality,
            'deliverySpeed' => $request->deliverySpeed,
            'review' => $request->review,
        ]);
    }
}