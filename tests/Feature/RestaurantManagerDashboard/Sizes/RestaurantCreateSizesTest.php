<?php

namespace Tests\Feature\RestaurantManagerDashboard;

use Illuminate\Http\Testing\File;
use phpDocumentor\Reflection\Types\Boolean;

class RestaurantCreateSizesTest extends RestaurantApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/sizes';

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
            'price' => $this->faker->numberBetween($min = 1500, $max = 6000),
            'published' => '1',
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
            'price' => 'integer',
            'published' => 'boolean',
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
        $this->assertFailCreate([], ['name', 'price']);
    }
}
