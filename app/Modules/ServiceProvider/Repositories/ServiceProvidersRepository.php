<?php

namespace App\Modules\ServiceProvider\Repositories;

use App\Modules\Cities\Models\City;
use Illuminate\Support\Facades\Mail;
use App\Modules\Countries\Models\Country;
use App\Modules\ServiceProvider\Models\ServiceProvider as Model;
use App\Modules\ServiceProvider\Filters\ServiceProvider as Filter;

use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;
use App\Modules\ServiceProvider\Resources\ServiceProvider as Resource;

class ServiceProvidersRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'serviceProviders';

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
    const DATA = ['firstName', 'lastName', 'email', 'tradeName', 'address', 'phoneNumber', 'commercialNumber', 'type', 'accept'];

    /**
     * Auto save uploads in this list
     * If it's an indexed array, in that case the request key will be as database column name
     * If it's associated array, the key will be request key and the value will be the database column name
     *
     * @const array
     */
    const UPLOADS = ['commercialImage'];

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
    const INTEGER_DATA = ['serviceType', 'joinRequest'];

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
    const BOOLEAN_DATA = ['published'];

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
        'country' => Country::class,
        'city' => City::class,
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
    const WHEN_AVAILABLE_DATA = ['serviceType', 'firstName', 'lastName', 'email', 'phoneNumber', 'tradeName', 'country', 'city', 'address', 'commercialNumber', 'commercialImage', 'published', 'type', 'joinRequest'];

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
     * @param   mixed $model
     * @param   \Illuminate\Http\Request $request
     * @return  void
     */
    protected function setData($model, $request)
    {
        $model->joinRequest = 0; // // 0 = NotShow / 1 = Accepted / 2 = Rejected
        $this->checkUserData($model, $request); // Method check Customer Data

        $dataForAdmin = $this->dataForAdmin(); // Method Get Admin Data

        $this->makeNotificationsForServiceProvider($dataForAdmin, $model->id); // Method Notifications For ServiceProvider
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
     * Method checkUserData
     *
     * @param $model $model
     * @param $request $request
     * check User Data
     * @return
     */
    public function checkUserData($model, $request)
    {
        $model->firstName = ($request->firstName) ? $request->firstName : user()->firstName;
        $model->lastName = ($request->lastName) ? $request->lastName : user()->lastName;
        $model->email = ($request->email) ? $request->email : user()->email;
        $model->phoneNumber = ($request->phoneNumber) ? $request->phoneNumber : user()->phoneNumber;

        return $model;
    }

    /**
     * Method dataForAdmin
     * data For Admin
     * @return val
     */
    public function dataForAdmin()
    {
        return $this->usersRepository->getByModel('name', 'admin')->first();
    }

    /**
     * Method messageTitel
     * message Titel
     * @return array
     */
    public function messageTitel()
    {
        $title = [];
        $title[0]['text'] = "New Request";
        $title[0]['localeCode'] = "en";
        $title[1]['text'] = 'تم طلب جديد';
        $title[1]['localeCode'] = 'ar';

        return $title;
    }

    /**
     * Method messageContent
     *
     * @return array
     */
    public function messageContent()
    {
        $content = [];
        $content[0]['text'] = 'A new request has been made to join as a service provider ';
        $content[0]['localeCode'] = "en";
        $content[1]['text'] = ' تم طلب جديد بوسطه الانضمام كمقدم خدمة';
        $content[1]['localeCode'] = 'ar';

        return $content;
    }

    /**
     * Method makeNotificationsForServiceProvider
     *
     * @param $dataForAdmin $dataForAdmin
     * @param $serviceProviderId $serviceProviderId
     *
     * @return void
     */
    public function makeNotificationsForServiceProvider($dataForAdmin, $serviceProviderId)
    {
        $this->notificationsRepository->create([
            'title' => $this->messageTitel(),
            'content' => $this->messageContent(),
            'type' => 'service provider',
            'user' => $dataForAdmin,
            'pushNotification' => false,
            'extra' => [
                'type' => 'service provider',
                'orderId' => $serviceProviderId,
            ],
        ]);
    }

    /**
     * Method serviceProviderAccepted
     *
     * @param $id $id
     *
     * @return void
     */
    public function serviceProviderAccepted(int $id)
    {
        // dd($id);
        $serviceProviderAccepted = Model::find($id);

        $serviceProviderAccepted->joinRequest = 1; // 0 = NotShow / 1 = Accepted / 2 = Rejected
        if ($serviceProviderAccepted->save()) {
            if ($serviceProviderAccepted->type == 'store') {
                $url = 'https://dashboard.diet.market/store/login';
            } elseif ($serviceProviderAccepted->type == 'restaurant') {
                $url = 'https://dashboard.diet.market/restaurant/login';
            } elseif ($serviceProviderAccepted->type == 'club') {
                $url = 'https://dashboard.diet.market/club/login';
            } elseif ($serviceProviderAccepted->type == 'nutritionSpecialist') {
                $url = 'https://dashboard.diet.market/clinic/login';
            }
            Mail::send([], [], function ($message) use ($serviceProviderAccepted, $url) {
                $message->to($serviceProviderAccepted->email)
                    ->subject('تم قبول الطلب')
                    // here comes what you want
                    ->setBody("
                    <p>
                        مرحبا بك {$serviceProviderAccepted->email}
                    </p>
                    </br>
                    </br>
                    <hr>
                    </br>
                    <p>
                    تم قبول الطلب حسابك كامقدم خدمة ويت ارفاق رابط للدخول الي لوحة التحكم
                    <a href='{$url}'>{$url}</a>
                    </p>
                ", 'text/html'); // assuming text/plain
            });
        }
    }

    /**
     * Method serviceProviderRejected
     *
     * @param int $id
     *
     * @return void
     */
    public function serviceProviderRejected(int $id)
    {
        $serviceProviderAccepted = Model::find($id);
        $serviceProviderAccepted->joinRequest = 2; // 0 = NotShow / 1 = Accepted / 2 = Rejected
        if ($serviceProviderAccepted->save()) {
            Mail::send([], [], function ($message) use ($serviceProviderAccepted) {
                $message->to($serviceProviderAccepted->email)
                    ->subject('تم رفض الطلب')
                    // here comes what you want
                    ->setBody("
                    <p>
                        مرحبا بك {$serviceProviderAccepted->email}
                    </p>
                    </br>
                    </br>
                    <hr>
                    </br>
                    <p>
                    تم رفض الطلب للعمل كمقدم خدمة في دايت ماركت
                    </p>
                ", 'text/html'); // assuming text/plain
            });
        }
    }
}
