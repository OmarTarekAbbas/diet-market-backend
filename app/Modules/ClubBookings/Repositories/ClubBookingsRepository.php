<?php

namespace App\Modules\ClubBookings\Repositories;

use App\Modules\Clubs\Models\Club;
use Illuminate\Support\Facades\Mail;
use App\Modules\Customers\Models\Customer;
use App\Modules\BranchesClubs\Models\BranchesClub;
use App\Modules\ClubBookings\Models\ClubBooking as Model;
use App\Modules\ClubBookings\Filters\ClubBooking as Filter;
use App\Modules\ClubBookings\Resources\ClubBooking as Resource;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class ClubBookingsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'clubBookings';

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
    const DATA = ['name', 'phone', 'time', 'status', 'nextStatus', 'notesAccepted', 'notesRejected'];

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
    const DATE_DATA = [
        'date',
    ];

    /**
     * Set the columns will be filled with single record of collection data
     * i.e [country => CountryModel::class]
     *
     * @const array
     */
    const DOCUMENT_DATA = [
        'customer' => Customer::class,
        'clubBranch' => BranchesClub::class,
        'club' => Club::class,

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
    const WHEN_AVAILABLE_DATA = ['notesAccepted', 'notesRejected'];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'inInt' => ['id'],
        'like' => [
            'status' => 'status',
        ],
        'int' => [
            'customer' => 'customer.id',
            'clubBranch' => 'clubBranch.id',
            'club' => 'club.id',
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

    const PENDING_STATUS = 'pending';

    const ACCEPTED_STATUS = 'accepted';

    const REJECTED_STATUS = 'rejected';

    const CANCELED_STATUS = 'canceled';

    const COMPLETED_STATUS = 'completed';

    /**
     * Next allowed status
     *
     * @const array
     */
    const NEXT_BOOKING_STATUS = [
        ClubBookingsRepository::ACCEPTED_STATUS => [ClubBookingsRepository::COMPLETED_STATUS, ClubBookingsRepository::CANCELED_STATUS],

        ClubBookingsRepository::PENDING_STATUS => [ClubBookingsRepository::ACCEPTED_STATUS, ClubBookingsRepository::COMPLETED_STATUS, ClubBookingsRepository::REJECTED_STATUS, ClubBookingsRepository::CANCELED_STATUS],

        ClubBookingsRepository::COMPLETED_STATUS => [],
        ClubBookingsRepository::CANCELED_STATUS => [],
        ClubBookingsRepository::REJECTED_STATUS => [],
        // ClubBookingsRepository::ACCEPTED_STATUS => []
    ];

    /**
     * Check if booking can be changed to the given status
     *
     * @param Model $clubBooking
     * @param string $nextStatus
     * @return bool
     */
    public function nextStatusIs($clubBooking, $nextStatus): bool
    {
        if (!isset(static::NEXT_BOOKING_STATUS[$clubBooking->status])) {
            return false;
        }

        return in_array($nextStatus, static::NEXT_BOOKING_STATUS[$clubBooking->status]);
    }

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


        if (!$model['id']) {
            $model->status = static::PENDING_STATUS;
            $model->nextStatus = static::NEXT_BOOKING_STATUS[$model->status] ?? [];
            // $club = $this->branchesClubsRepository->getQuery()->where('id', (int) $request->clubBranch)->first();
            // if ($club) {
            //     $model->club = $club['club']['id'];
            // }


            if (user() && user()->accountType() == 'customer') {
                $model->customer = user()->sharedInfo();
            }


            if (!$request->name) {
                $model->name = user()->firstName . ' ' . user()->lastName;
            }

            if (!$request->name) {
                $model->phone = user()->phoneNumber;
            }
        }
    }

    /**
     *  createBooking dates
     *
     * @param $request $request
     *
     * @return void
     */
    public function createBooking($request)
    {
        $branchId = $request->clubBranch;
        $clubs = $this->branchesClubsRepository->getQuery()->where('id', (int) $branchId)->get();
        foreach ($request->bookingDates as  $date) {
            // dd($club['club']['id']);
            foreach ($clubs as $key => $club) {
                $this->clubBookingsRepository->create([
                    'clubBranch' => $branchId,
                    'date' => $date['date'],
                    'time' => $date['time'],
                    'club' => $club['club']['id'],
                    'nextStatus' => static::NEXT_BOOKING_STATUS[$request->status] ?? [],

                ]);
            }


            $admin = $this->usersRepository->getByModel('name', 'admin');
            $club = $this->clubsRepository->get((int) $club['club']['id']);
            $customer = user()->firstName;
            Mail::send([], [], function ($message) use ($admin, $club, $customer) {
                $url = 'https://dashboard.diet.market/reservations-clubs';
                $message->to($admin['email'])
                    ->subject('يوجد حجز جديد')
                    ->setBody("
                <p>
                    مرحبا بك {$admin['name']}
                </p>
                <p>
                اسم النادي {$club['name'][0]['text']}
                </br>
                </br>
                <hr>
                </br>
                بواسطة  [{$customer}]
                </br>
                </br>
                <hr>
                    يمكنك مراجعة طلب الحجز والموافقة من خلال
                </br>
                <a href='{$url}'>
                [{$url}]</a>
                </p>
                ", 'text/html'); // assuming text/plain
            });

            $clubManager = $this->clubManagersRepository->getByModel('club.id', (int) $club['id']);
            // dd($clubManager);
            if ($clubManager) {
                Mail::send([], [], function ($message) use ($clubManager, $club, $customer) {
                    $url = 'https://dashboard.diet.market/club/reservations-clubs';
                    $message->to($clubManager['email'])
                        ->subject('يوجد حجز جديد')
                        ->setBody("
                    <p>
                        مرحبا بك {$clubManager['name']}
                    </p>
                    <p>
                    اسم النادي {$club['name'][0]['text']}
                    </br>
                    </br>
                    <hr>
                    </br>
                    بواسطة  [{$customer}]
                    </br>
                    </br>
                    <hr>
                        يمكنك مراجعة طلب الحجز والموافقة من خلال
                    </br>
                    <a href='{$url}'>
                    [{$url}]</a>
                    </p>
                    ", 'text/html'); // assuming text/plain
                });
            }
        }
    }

    /**
     * Change Booking Status
     *
     * @param $clubBookingId $clubBookingId
     * @param $status $status
     *
     * @return void
     */
    public function changeStatus($clubBookingId, $status)
    {
        if (is_numeric($clubBookingId)) {
            $clubBooking = $this->getModel($clubBookingId);
        }

        if (!$clubBooking) {
            return null;
        }

        $clubBooking->status = $status;
        if ($status == 'accepted') {
            $clubBooking->notesAccepted = request()->notesAccepted ?? '';
        } else {
            $clubBooking->notesRejected = request()->notesRejected ?? '';
        }
        $clubBooking->save();

        return $clubBooking;
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        if ($id = $this->option('id')) {
            $this->query->where('id', (int) $id);
        }

        if ($customer = $this->option('customer')) {
            $this->query->where('customer.id', (int) $customer);
        }
    }

    /**
     * Method clubBookings
     *
     * @return void
     */
    public function clubBookings($id)
    {
        $customer = user();
        if ($customer) {
            $clubBookings = $this->clubBookingsRepository->getQuery()->where('club.id', (int) $id)->where('customer.id', $customer['id'])->first();
            if ($clubBookings) {
                return true;
            }
        }
    }
}
