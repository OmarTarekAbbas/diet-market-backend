<?php

namespace Tests\Feature\RestaurantManagerDashboard;

use Illuminate\Http\Testing\File;

class RestaurantCreateMealsTest extends RestaurantApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/meals';

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
            'image' => File::create('img1.png', 100),
            'protein' => 300,
            'carbohydrates' => 900,
            'fat' => 20,
            'published' => '1',
            'categories' => '3',
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
            'image' => 'string',
            'protein' => 'integer',
            'carbohydrates' => 'integer',
            'fat' => 'integer',
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
        $this->assertFailCreate([], ['name', 'protein', 'carbohydrates', 'fat', 'image', 'categories']);
    }
}
