<?php

namespace Tests\Feature\RestaurantManagerDashboard;

use Illuminate\Http\Testing\File;
use phpDocumentor\Reflection\Types\Boolean;

class RestaurantUpdateSizesTest extends RestaurantApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/size';

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
                    'text' => 'Update Size En',
                    'localCode' => 'en',
                ],
                [
                    'text' => 'Update Size Ar',
                    'localCode' => 'ar',
                ]
            ],
            'price' => $this->faker->numberBetween($min = 1500, $max = 6000),
            'published' => '1',
            '_method' => 'PUT',
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
        $this->successUpdate(39, $this->fullData());
    }
}
