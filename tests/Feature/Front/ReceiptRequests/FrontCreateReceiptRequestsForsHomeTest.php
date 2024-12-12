<?php

namespace Tests\Feature\Front;

use Illuminate\Http\Testing\File;

class FrontCreateReceiptRequestsForHomeTest extends FrontApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/receiptRequests/home';

    /**
     * Set base full accurate data
     * This includes required and optional data
     *
     * @return array
     */
    protected function fullData(): array
    {
        return [
            'firstName' => 'OmarTarekTest',
            'lastName' => 'OmarTarekTest',
            'phoneNumber' => '01123656796',
            'city' => '1',
            'items' => [
                '0' => '22',
            ],
            'type' => 'home',
            'residentialQuarter' => 'عبده باشا',
            'address' => '12 Boutros Ghaly Street, Heliopolis',
        ];
    }

    /**
     * Set what response record is expected to be returned
     *
     * @return array
     */
    protected function recordShape(): array
    {
        return [
            'firstName' => 'string',
            'lastName' => 'string',
            'phoneNumber' => 'string',
            'city' => 'array',
            'items' => 'array',
            'type' => 'string',
            'residentialQuarter' => 'string',
            'address' => 'string',
        ];
    }

    /**
     * Method testSuccessCreate
     * test Success Create
     * @return void
     */
    public function testSuccessCreate()
    {
        $this->successCreate($this->fullData(), $this->recordShape());
    }
}
