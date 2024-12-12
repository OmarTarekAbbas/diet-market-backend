<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Http\Testing\File;

class AdminCreateRestaurantTest extends AdminApiTestCase
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
                    'text' => $this->faker->name,
                    'localCode' => 'en',
                ],
                [
                    'text' => $this->faker->name,
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
            'logoImage' => File::create('img1.png', 100),
            'commercialRegisterImage' => File::create('img2.png', 100),
            'commercialRegisterNumber' => '055555',
            'minimumOrders' => '300',
            'city' => '1',
            'published' => '1',
            'delivery' => '1',
            'workTimes' => [
                [
                    'day' => 'saturday',
                    'available' => 'yes',
                    'open' => '8:00 am',
                    'close' => '10:00 pm',
                ],
                [
                    'day' => 'thr',
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
        return [
            'name' => 'array',
            'logoText' => 'array',
            'address' => 'array',
            'logoImage' => 'string',
            'city' => 'array',
            'published' => 'boolean',
            'delivery' => 'boolean',
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

    /**
     * Test Full Required Data Validation
     */
    public function testRequiredDataValidation()
    {
        $this->assertFailCreate([], ['name', 'logoText', 'address', 'commercialRegisterNumber', 'minimumOrders', 'city', 'delivery', 'workTimes']);
    }
}
