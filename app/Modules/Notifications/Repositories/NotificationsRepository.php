<?php

namespace App\Modules\Notifications\Repositories;

use Illuminate\Support\Facades\App;
use App\Modules\Notifications\Services\PushNotifications;
use App\Modules\Notifications\Models\Notification as Model;
use App\Modules\Notifications\Filters\Notification as Filter;
use App\Modules\Notifications\Resources\Notification as Resource;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class NotificationsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'notifications';

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
    const DATA = ['title', 'content', 'type', 'extra'];

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
     * Geo Location data
     *
     * @const array
     */
    const LOCATION_DATA = [];

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
    const BOOLEAN_DATA = ['pushNotification'];

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
        '=' => [
            'user' => 'user.id',
            'userType' => 'userType',
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
     * User types repositories
     *
     * @const array
     */
    const USERS_REPOSITORIES = [
        'user' => 'users',
        'customer' => 'customers',
        'consultant' => 'consultants',
        'RestaurantManager' => 'restaurantManagers',
        'StoreManager' => 'storeManagers',
        'ClubManager' => 'clubManagers',
        'NutritionSpecialistManger' => 'nutritionSpecialistMangers',
        'deliveryMen' => 'deliveryMen',
    ];

    protected $notifiedUser;

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

        $model->seen = false;

        if ($request->userType) {
            $user = $this->deliveryMenRepository->getModel($request->user);
            $this->notifiedUser = $user;
            $model->user = $user->sharedInfo();
            $model->userType = $user->accountType();
        } else {
            $model->user = $request->user->sharedInfo();
            $model->userType = $request->user->accountType();
            $this->notifiedUser = $request->user;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onCreate($model, $request)
    {
        if ($request->pushNotification) {
            $this->pushNotification($model, $this->notifiedUser);
        }

        $this->updateTotalNotificationsForNotificationUser($model);
    }

    /**
     * {@inheritdoc}
     */
    public function onDelete($model, $id)
    {
        $this->updateTotalNotificationsForNotificationUser($model);
    }

    /**
     * Mark as seen
     *
     * @param int $id
     * @return void
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    public function markAsSeen($id)
    {
        $notification = $this->getModel($id);

        $notification->seen = true;

        $notification->save();

        $this->updateTotalNotificationsForNotificationUser($notification);
    }

    /**
     * Mark all notifications as seen for given user
     *
     * @param Model $user
     * @return void
     */
    public function markAllAsSeen($user)
    {
        $this->getUserQuery($user)->update([
            'seen' => true,
        ]);

        $user->totalNotifications = 0;
        $user->save();
    }

    /**
     * Delete all notifications for the given user
     *
     * @param mixed $user
     * @return void
     * @throws \Exception
     */
    public function deleteAllFor($user)
    {
        $this->getUserQuery($user)->delete();

        $user->totalNotifications = 0;
        $user->save();
    }

    /**
     * Update total unseen notifications for user
     *
     * @param Model $user
     * @param string $userType
     * @return void
     */
    public function updateTotalUnSeenNotifications($user)
    {
        $totalUnseenNotifications = $this->getUserQuery($user)->where('seen', false)->count();

        $user->totalNotifications = $totalUnseenNotifications;

        $user->save();

        $currentUser = user();

        if (user() && user()->is($user)) {
            $currentUser->totalNotifications = $totalUnseenNotifications;
        }
    }

    /**
     * Check if the given notification id belong to the given user
     *
     * @param int $id
     * @param Model $user
     * @return bool
     */
    public function notificationBelongTo($id, $user): bool
    {
        return $this->getUserQuery($user)->where('id', (int) $id)->exists();
    }

    /**
     * Get a query Filtered by the given user
     *
     * @param AccountableUser $user
     * @return Model
     */
    protected function getUserQuery($user)
    {
        return $this->getQuery()->where("user.id", $user->id)->where('userType', $user->accountType());
    }

    /**
     * Push notification for the given user
     *
     * @param $model
     * @param User $user
     * @return void
     */
    public function pushNotification($model, $user)
    {
        $pushNotifications = App::make(PushNotifications::class);
        $pushNotifications->toUser($user, [
            'title' => $model->title,
            'body' => $model->content,
            'data' => $model->extra,
            'image' => $model->image,
        ]);
    }

    /**
     * Get notified user model
     *
     * @param Model $notification
     * @return UserModel
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    public function getNotifiedUser($notification)
    {
        $userType = $notification->userType;
        $userId = $notification->user['id'];

        $userRepository = repo(static::USERS_REPOSITORIES[$userType]);

        return $userRepository->getModel($userId);
    }

    /**
     * Update total unseen notifications for the user of the given notification
     *
     * @param Model $notification
     * @return void
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    private function updateTotalNotificationsForNotificationUser($notification)
    {
        $this->updateTotalUnSeenNotifications($this->getNotifiedUser($notification));
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        $user = user();

        if ($user && $user->accountType() == 'customer') {
            $this->query->where('createdAt', '>', $user->createdAt);
        }
    }

    /**
     * Method deleteNotificationsByDelivery
     *
     * @param $id $id
     * @param $model $model
     *
     * @return void
     */
    public function deleteNotificationsByDelivery($id, $model)
    {
        $notifications = Model::where('extra.typeId', (int) $id)->get();
        foreach ($notifications as $key => $notification) {
            $notification->delete();
        }
    }
}
