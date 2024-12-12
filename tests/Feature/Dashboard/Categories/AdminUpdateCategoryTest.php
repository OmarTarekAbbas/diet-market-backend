<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Http\Testing\File;

class AdminUpdateCategoryTest extends AdminApiTestCase
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
                    'text' => 'update cat',
                    'localCode' => 'en',
                ],
                [
                    'text' => 'تعديل الكات',
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
        return [];
    }

    /**
     * Method testSuccessUpdate
     * test Success Update
     * @return void
     */
    public function testSuccessUpdate()
    {
        $this->successUpdate(30, $this->fullData());
    }
}
