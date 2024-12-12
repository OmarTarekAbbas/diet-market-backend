<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Http\Testing\File;

class AdminUpdateRestaurantTest extends AdminApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/restaurants';

    /**
     * Set base full accurate data
     * This includes required and optional data
     *
     * @return array
     */
    protected function fullData(): array
    {
        return [
            'name' => [
                [
                    'text' => 'test omar rowwad ',
                    'localCode' => 'en',
                ],
                [
                    'text' => ' عمر طارق رواد',
                    'localCode' => 'ar',
                ]
            ],
            'logoText' => [
                [
                    'logoText' => $this->faker->name,
                    'localCode' => 'en',
                ],
                [
                    'logoText' => $this->faker->name,
                    'localCode' => 'ar',
                ]
            ],
            'address' => [
                [
                    'text' => $this->faker->address,
                    'localCode' => 'en',
                ],
                [
                    'text' => $this->faker->address,
                    'localCode' => 'ar',
                ]
            ],
            'logoImage' => File::create('img15.png', 100),
            'commercialRegisterImage' => File::create('img16.png', 100),
            'commercialRegisterNumber' => '125899999',
            'minimumOrders' => '399',
            'city' => '1',
            'published' => '1',
            'delivery' => '1',
            'workTimes' => [
                [
                    'day' => 'omar',
                    'available' => 'yes',
                    'open' => '8:00 am',
                    'close' => '10:00 pm',
                ],
                [
                    'day' => 'omar2',
                    'available' => 'yes',
                    'open' => '8:00 am',
                    'close' => '10:00 pm',
                ]
            ],
        ];
    }

    /**
     * Set what response record is expected to be returned
     *
     * @return array
     */
    protected function recordShape(): array
    {
        return [];
    }



    /**
     * Method testSuccessUpdate
     * test Success Update
     * @return void
     */
    public function testSuccessUpdate()
    {
        $this->successUpdate(368, $this->fullData());
    }
}
