<?php

namespace Tests\Feature\Front;

use Illuminate\Http\Testing\File;

class FrontCreateReceiptRequestsForRestaurantsTest extends FrontApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/receiptRequests/restaurants';

    /**
     * Set base full accurate data
     * This includes required and optional data
     *
     * @return array
     */
    protected function fullData(): array
    {
        return [
            'items' => [
                '0' => '22',
            ],
            'receiptRequestsHours' => [
                [
                    'start' => '9',
                ],
                [
                    'end' => '10',

                ]
            ],
            'notes' => $this->faker->text,
            'type' => 'restaurant',
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
            'items' => 'array',
            'receiptRequestsHours' => 'array',
            'notes' => 'string',
            'type' => 'string',
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
