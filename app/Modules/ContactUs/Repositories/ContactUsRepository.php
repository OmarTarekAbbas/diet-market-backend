<?php

namespace App\Modules\ContactUs\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Modules\TypeContactUs\Models\TypeContactU;
use App\Modules\ContactUs\Models\ContactU as Model;
use App\Modules\ContactUs\Filters\ContactU as Filter;
use App\Modules\ContactUs\Resources\ContactU as Resource;

use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class ContactUsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'contactUs';

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
    const DATA = ['name', 'email', 'phoneNumber', 'subject', 'message', 'department'];

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
     * Set the columns will be filled with single record of collection data
     * i.e [country => CountryModel::class]
     *
     * @const array
     */
    const DOCUMENT_DATA = [
        'type' => TypeContactU::class,
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
        '=' => [
            'type',
            'department',
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
     */
    protected function setData($model, $request)
    {
        if (!$model->id && !$request->name) {
            $model->name = $request->firstName . ' ' . $request->lastName;
        }
    }

    /**
     * Reply to the given contact id
     *
     * @param int $id
     * @param Request $request
     * @return void
     */
    public function reply($id, Request $request)
    {
        $contact = $this->getModel($id);

        if (!$contact || $contact->reply) {
            return;
        }

        $contact->reply = $request->reply;

        $contact->replied = true;

        Mail::send([], [], function ($mailer) use ($contact) {
            $mailer->to($contact->email)
                ->subject("الرد على رسالة التواصل #" . $contact->id)
                ->setBody("
                    {$contact->reply}
                    <h1>نص الرسالة الاصلية</h1>
                    <p>{$contact->message}</p>
                    ", 'text/html');
        });

        $contact->repliedAt = time();
        $contact->repliedBy = user()->sharedInfo();

        $contact->save();
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        
    }
}
