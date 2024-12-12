<?php

namespace App\Modules\Clubs\Repositories;

use Illuminate\Support\Facades\Mail;
use App\Modules\Customers\Models\Customer;
use App\Modules\Clubs\Models\ClubReview as Model;
use App\Modules\Clubs\Filters\ClubReview as Filter;

use App\Modules\Clubs\Resources\ClubReview as Resource;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class ClubReviewsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'clubReviews';

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
    const DATA = ['review'];

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
    const INTEGER_DATA = ['rate', 'orderId', 'clubId'];

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
    const DOCUMENT_DATA = [
        'customer' => Customer::class,
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
        'review', 'customer', 'rate', 'clubId', 'published', 'orderId',
    ];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'boolean' => ['published'],
        // 'int' => [
        //     'id' => 'id',
        //     'clubId' => 'clubId',
        // ],
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
    const ITEMS_PER_PAGE = 10;

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
            $model->published = false;
            $model->customer = user()->only(['id', 'firstName', 'lastName']);
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

        if ($clubId = $this->option('club')) {
            $this->query->where('clubId', (int) $clubId);
        }
        if ($store = $this->option('store')) {
            $this->query->where('storeId', (int) $store);
        }

        if ($orderId = $this->option('orderId')) {
            $this->query->where('orderId', (int) $orderId);
        }
        if ($id = $this->option('id')) {
            $this->query->where('id', (int) $id);
        }

        if (($sortRating = $this->option('sortRating')) && in_array($this->option('sortRating'), ['desc', 'asc'])) {
            $this->query->orderBy('rate', $sortRating);
        }

        // highestRating
        //lowestRating
        //latest
        //oldest

        if ($sort = $this->option('sort')) {
            if ($sort == 'highestRating') {
                $this->query->orderBy('rate', 'desc');
            }
            if ($sort == 'lowestRating') {
                $this->query->orderBy('rate', 'asc');
            }
            if ($sort == 'latest') {
                $this->query->latest();
            }
            if ($sort == 'oldest') {
                $this->query->oldest();
            }
        }

        if ($this->option('highestRating')) {
            $this->query->orderBy('rate', 'desc');
        }

        if ($this->option('lowestRating')) {
            $this->query->orderBy('rate', 'asc');
        }

        if ($this->option('latest')) {
            $this->query->latest();
        }

        if ($this->option('oldest')) {
            $this->query->oldest();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function onCreate($model, $request)
    {
        $reviews = $this->getQuery()->where('clubId', $model->clubId);
        $avg = $reviews->avg('rate');
        $total = $reviews->count();
        $this->clubsRepository->updateRate($model->clubId, $avg, $total);
        $this->ordersRepository->saveClubsReview($model);

        $admin = $this->usersRepository->getByModel('name', 'admin');
        $club = $this->clubsRepository->get((int) $model->clubId);
        $customer = user()->firstName;
        // dd($club['name']);
        Mail::send([], [], function ($message) use ($admin, $club, $customer) {
            $url = 'https://dashboard.diet.market/clubs-reviews';
            $message->to($admin['email'])
                ->subject('تقييم جديد')
                ->setBody("
                <p>
                    مرحبا بك {$admin['name']}
                </p>
                <p>
                يوجد تفييم جديد {$club['name'][0]['text']}
                </br>
                </br>
                <hr>
                </br>
                بواسطة  [{$customer}]
                </br>
                </br>
                <hr>
                    يمكنك مراجعة التقييم والموافقة من خلال
                </br>
                <a href='{$url}'>
                [{$url}]</a>
                </p>
                ", 'text/html'); // assuming text/plain
        });
    }

    /**
     * check if rating this product for this order before
     *
     * @param int $orderId
     * @param int $productId
     * @return mixed
     */
    public function isRating(int $orderId, int $clubId)
    {
        return $this->getQuery()->where('orderId', $orderId)->where('clubId', $clubId)->exists();
    }

    /**
     * Method clubsReview
     *
     * @param $options $options
     *
     * @return void
     */
    public function clubsReview($options)
    {
        $clubs = [];
        $clubReviews = $this->clubReviewsRepository->list($options);
        foreach ($clubReviews as $key => $clubReview) {
            $club = $this->clubsRepository->get($clubReview->clubId);
            $clubReview['club'] = $club;
            $clubs[] = $clubReview;
        }

        return $clubs;
    }

    /**
     * Method clubsReview
     *
     * @param $options $options
     *
     * @return void
     */
    public function clubsReviewId($id)
    {
        $record = $this->clubReviewsRepository->get((int) $id);
        $clubReview = $this->clubsRepository->get((int) $record->clubId);
        $record['club'] = $clubReview;

        return $record;
    }

    /**
     * Method updateCustomerReviews
     *
     * @param $reviews $reviews
     *
     * @return void
     */
    public function updateCustomerReviews($reviews)
    {
        $reviewDatas = Model::where('customer.id', $reviews['id'])->get();
        foreach ($reviewDatas as $reviewData) {
            $reviewData->update([
                'customer' => $reviews->only(['id', 'firstName', 'lastName']),
            ]);
        }
    }
}
