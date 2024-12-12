<?php

namespace App\Modules\CustomerGroups\Repositories;

use App\Modules\CustomerGroups\Models\CustomerGroup as Model;
use App\Modules\CustomerGroups\Filters\CustomerGroup as Filter;
use App\Modules\CustomerGroups\Resources\CustomerGroup as Resource;

use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class CustomerGroupsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'customerGroups';

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
    const DATA = ['name', 'conditionType', 'nameGroup'];

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
    const INTEGER_DATA = ['totalCustomers'];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = [
        'specialDiscount', 'conditionValue',
    ];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = ['freeShipping', 'freeExpressShipping', 'published'];

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
        'conditionType', 'name', 'conditionType', 'specialDiscount', 'conditionValue', 'freeShipping', 'freeExpressShipping', 'totalCustomers',
    ];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'like' => [
            'name' => 'name.text',
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

        if (!$model->totalCustomers) {
            $model->totalCustomers = 0;
        }
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        if ($id = $this->option('id')) {
            $this->query->whereIn('id', array_map('intval', (array) $id));
        }
    }

    /**
     * find group for customer & update total customer for group
     *
     * @param $customer
     */
    public function findGroupForCustomer($customer)
    {
        $groups = $this->getQuery()->where('published', true)
            ->where(function ($query) use ($customer) {
                $query->orwhere(function ($totalPurchaseAmount) use ($customer) {
                    $totalPurchaseAmount->where('conditionType', 'totalPurchaseAmount')->where('conditionValue', '<=', $customer->totalOrdersPurchases);
                });

                $query->orWhere(function ($totalOrders) use ($customer) {
                    $totalOrders->where('conditionType', 'totalOrders')->where('conditionValue', '<=', $customer->totalOrders);
                });
            })


            ->orderBy('conditionValue', 'desc')
            ->get()
            ->groupBy('conditionType');

        if (!empty($groups['totalPurchaseAmount'])) {
            $firstGroup = $groups['totalPurchaseAmount'];
        } elseif (!empty($groups['totalOrders'])) {
            $firstGroup = $groups['totalOrders'];
        }

        if (!empty($firstGroup)) {
            $firstGroup = $firstGroup->sortBy([['conditionValue', 'desc']])->first();

            $this->customersRepository->updateGroup($customer->id, $firstGroup);

            $firstGroup->totalCustomers = $this->customersRepository->total([
                'group' => $firstGroup->id,
            ]);

            $firstGroup->save();

            // 'specialDiscount'', 'freeShipping', 'freeExpressShipping'
            $title = '';
            $content = '';
            if ($firstGroup->specialDiscount) {
                $title = 'specialDiscount';
                $content = 'specialDiscount';
            } elseif ($firstGroup->freeShipping) {
                $title = 'freeShipping';
                $content = 'freeShipping';
            } elseif ($firstGroup->freeExpressShipping) {
                $title = 'freeExpressShipping';
                $content = 'freeExpressShipping';
            }

            $this->notificationsRepository->create([
                'title' => trans('notifications.newCustomerGroup.title.' . $title),
                'content' => trans('notifications.newCustomerGroup.content.' . $content, ['value' => $firstGroup->specialDiscount]),
                'type' => 'newCustomerGroup',
                'user' => $customer,
                'pushNotification' => true,
                'extra' => [
                    'type' => 'newCustomerGroup',
                ],
            ]);
        }
    }

    /**
     * check available group
     *
     * @param $id
     * @return bool
     */
    public function checkAvailable($id): bool
    {
        $group = $this->getModel($id);

        if ($group && $group->published) {
            return true;
        }

        return false;
    }

    /**
     * @param $model
     * @param $id
     * {@inheritDoc}
     */
    public function onDelete($model, $id)
    {
        $this->customersRepository->removeCustomerGroup($id);
    }

    /**
     * Method onUpdate
     *
     * @param $model $model
     * @param $request $request
     * @param $oldModel $oldModel
     *
     * @return void
     */
    public function onUpdate($model, $request, $oldModel)
    {
        $this->customersRepository->updateCustomerGroup($model);
    }

    /**
     * Method createCustomerGroups
     *
     * @param $request $request
     *
     * @return void
     */
    public function createCustomerGroups($request)
    {
        $request = ($request) ? $request : request();
        $createCustomerGroups = Model::find((int) $request->group);

        $createCustomerGroups->totalCustomers = $createCustomerGroups->totalCustomers + 1;
        $createCustomerGroups->save();
    }

    /**
     * Method updateCustomerGroups
     *
     * @param $request $request
     *
     * @return void
     */
    public function updateCustomerGroups($request, $model)
    {
        if ($model['group'] && (int) $request->group != $model['group']['id']) {
            $createCustomerGroups = Model::find((int) $request->group);
            $createCustomerGroups->totalCustomers = $createCustomerGroups->totalCustomers + 1;
            $createCustomerGroups->save();

            $createCustomerGroups = Model::find((int) $model['group']['id']);
            $createCustomerGroups->totalCustomers = $createCustomerGroups->totalCustomers - 1;
            $createCustomerGroups->save();
        } else {
            $createCustomerGroups = Model::find((int) $request->group);
            $createCustomerGroups->totalCustomers = $createCustomerGroups->totalCustomers + 1;
            $createCustomerGroups->save();
        }
    }

    /**
     * Method deleteCustomerGroups
     *
     * @param $model $model
     *
     * @return void
     */
    public function deleteCustomerGroups($model)
    {
        $createCustomerGroups = Model::find((int) $model['group']['id']);
        $createCustomerGroups->totalCustomers = $createCustomerGroups->totalCustomers - 1;
        $createCustomerGroups->save();
    }
}
