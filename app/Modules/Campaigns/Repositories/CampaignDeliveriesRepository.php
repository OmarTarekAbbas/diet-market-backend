<?php

namespace App\Modules\Campaigns\Repositories;

use Illuminate\Support\Facades\App;
use App\Modules\DeliveryMen\Models\DeliveryMan;
use App\Modules\Notifications\Services\PushNotifications;
use App\Modules\Campaigns\Models\CampaignDelivery as Model;
use App\Modules\Campaigns\Filters\CampaignDelivery as Filter;
use App\Modules\Campaigns\Resources\CampaignDelivery as Resource;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class CampaignDeliveriesRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'campaignDeliveries';

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
    const DATA = ['title', 'content', 'deliveries'];

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
    const MULTI_DOCUMENTS_DATA = [
        // 'deliveries' => DeliveryMan::class
    ];

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
            'id',
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
    }

    /**
     * {@inheritdoc}
     */
    public function onCreate($model, $request)
    {
        // $notifications = App::make(PushNotifications::class);
        // $notifications->sendTopic('newCampaignDelivery', [
        //     'title' => $model->title,
        //     'body' => $model->content,
        //     'image' => $model->image ? url($model->image) : '',
        //     'data' => [
        //         'type' => 'newCampaignDelivery',
        //         // 'typeId' => $model->typeId,
        //         'data' => $model->data
        //     ],
        // ]);

        $deliveryMens = $this->deliveryMenRepository->getModel(array_map('intval', $model->deliveries));
        foreach ($deliveryMens as $key => $deliveryMen) {
            $this->notificationsRepository->create([
                'title' => $model->title,
                'content' => $model->content,
                'type' => 'newCampaignDelivery',
                'user' => $deliveryMen,
                'image' => $model->image ? url($model->image) : '',
                'pushNotification' => true,
                'extra' => [
                    'type' => 'newCampaignDelivery',
                    'typeId' => $model->id,
                    'notificationCount' => $deliveryMen->totalNotifications + 1,
                    // 'data' => $model->data
                ],
            ]);
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
     * Method onDelete
     *
     * @param $model $model
     * @param $id $id
     *
     * @return void
     */
    public function onDelete($model, $id)
    {
        $this->notificationsRepository->deleteNotificationsByDelivery($id, $model);
    }
}
