<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Http\Testing\File;

class AdminCreateCategoryTest extends AdminApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/categories';

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
            'description' => [
                [
                    'text' => $this->faker->text,
                    'localCode' => 'en',
                ],
                [
                    'text' => $this->faker->text,
                    'localCode' => 'ar',
                ]
            ],
            'type' => 'food',
            'restaurant' => '26',
            'image' => File::create('img1.png', 100),
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
            'description' => 'array',
            'type' => 'string',
            'restaurant' => 'array',
            'image' => 'string',
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
        $this->assertFailCreate([], ['name', 'description', 'type']);
    }
}
