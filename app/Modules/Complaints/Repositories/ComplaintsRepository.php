<?php

namespace App\Modules\Complaints\Repositories;

use Illuminate\Http\Request;
use App\Jobs\NotificationsJob;
use App\Modules\Orders\Models\Order;
use App\Modules\Customers\Models\Customer;
use App\Modules\Complaints\Models\Complaint as Model;
use App\Modules\Complaints\Filters\Complaint as Filter;
use App\Modules\Complaints\Resources\Complaint as Resource;

use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class ComplaintsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'complaints';

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
    const DATA = ['orderId', 'reason', 'note'];

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

    const UPLOADS_KEEP_FILE_NAME = false;

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
        'customer' => Customer::class,
        'order' => Order::class,
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
        'inInt' => [
            'id',
        ],
        'like' => [
            'name' => 'name.text',
        ],
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
     * @param mixed $model
     * @param \Illuminate\Http\Request $request
     * @return  void
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    protected function setData($model, $request)
    {
        $model->customer = user()->sharedInfo();

        // get order info and set client or provider info in data
        $order = $this->ordersRepository->getModel($request->get('orderId'));

        $model->order = $order;
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        if ($order = $this->option('order')) {
            $this->query->where('order.id', (int) $order);
        }

        if ($customerId = $this->option('customerId')) {
            $this->query->where('customer.id', (int) $customerId);
        }

        if ($customerName = $this->option('customerName')) {
            $this->query->whereLike('customer.name', $customerName);
        }
    }

    /**
     * send replay complaint to client | provider by push notifications
     *
     * @param Request $request
     * @param int $complaintId
     * @return \Illuminate\Http\Resources\Json\JsonResource|\JsonResource
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    public function replay(Request $request, int $complaintId)
    {
        $complaint = $this->getModel($complaintId);

        $user = $this->ordersRepository->get($request->client);

        $complaint->associate([
            'title' => $request->title,
            'content' => $request->message,
            'replayBy' => user()->sharedInfo(),
        ], 'replay');

        $complaint->save();

        dispatch(new NotificationsJob([
            'title' => $request->title,
            'content' => $request->message,
            'type' => 'replayComplaint',
            'user' => $user,
            'pushNotification' => true,
            'extra' => [
                'type' => 'replayComplaint',
                'complaintId' => $complaint->id,
            ],
        ]));

        return $this->wrap($complaint);
    }
}
