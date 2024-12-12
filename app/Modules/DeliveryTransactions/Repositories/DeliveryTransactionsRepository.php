<?php

namespace App\Modules\DeliveryTransactions\Repositories;

use Carbon\Carbon;
use App\Modules\Orders\Models\OrderDelivery;
use App\Modules\DeliveryMen\Models\DeliveryMan;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

use App\Modules\DeliveryTransactions\Models\DeliveryTransaction as Model;
use App\Modules\DeliveryTransactions\Filters\DeliveryTransaction as Filter;
use App\Modules\DeliveryTransactions\Resources\DeliveryTransaction as Resource;

class DeliveryTransactionsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'deliveryTransactions';

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
    const DATA = ['deliveryStatus','paymentMethod'];

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
        'amount','deliveryCommission','commissionDiteMarket','totalAmountOrder',
    ];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = ['published'];

    /**
     * Set columns list of date values.
     *
     * @cont array
     */
    const DATE_DATA = [];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array|true
     */
    const WHEN_AVAILABLE_DATA = [];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [

        'int' => [
            'id',
            'deliveryMan' => 'deliveryMan.id',
            'orderDelivery' => 'orderDelivery',
        ],
        'like' => [
            'status' => 'deliveryStatus',
        ],
        'boolean' => ['published'],

    ];

    const DOCUMENT_DATA = [
        // 'deliveryMan' => DeliveryMan::class,
        // 'orderDelivery' => OrderDelivery::class,
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
    const PAGINATE = true;

    /**
     * Number of items per page in pagination
     * If set to null, then it will taken from pagination configurations
     *
     * @const int|null
     */
    const ITEMS_PER_PAGE = 15;


    // const DELIVERING_STATUS = 'delivering';
    // const DELIVERED_AND_RECEIVED_STATUS = 'deliveredAndReceived';
    // const DELIVERED_NOT_RECEIVED_STATUS = 'deliveredNotReceived';
    // const CANCELLED_BY_CLIENT_STATUS = 'cancelledByClient';
    // const CANCELLED_BY_ADMIN_STATUS = 'cancelledByAdmin';

    /**
     * Set any extra data or columns that need more customizations
     * Please note this method is triggered on create or update method
     * If the model id is present, then its an update operation otherwise its a create operation.
     *
     * @param   Model $model
     * @param   \Illuminate\Http\Request $request
     * @return  void
     */
    protected function setData($model, $request)
    {
        

        if (!$model->id) {
            // $model->commission = $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission');
            // $model->deliveryStatus = static::DELIVERING_STATUS;
            $model->published = $request->published ?: false;
        }
    }

    /**
     * Manage Selected Columns
     *
     * @return void
     */
    protected function select()
    {
        
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        if ($this->option('date')) {
            $startOfDay = Carbon::parse($this->option('date'));
            $endOfDay = Carbon::parse($this->option('date'));
            $this->query->whereBetween('createdAt', [$startOfDay->startOfDay(), $endOfDay->endOfDay()]);
        }
        if ($this->option('from')) {
            $startOfDay = Carbon::parse($this->option('from'));
            $endOfDay = Carbon::parse($this->option('to'));
            $this->query->whereBetween('createdAt', [$startOfDay->startOfDay(), $endOfDay->endOfDay()]);
        }
    }

    /**
     * Get a specific record with full details
     *
     * @param  int id
     * @return mixed
     */
    public function get(int $id)
    {
        
    }
}
