<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Http\Testing\File;

class AdminUpdateRestaurantManagerTest extends AdminApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/restaurantmanager';

    /**
     * Set base full accurate data
     * This includes required and optional data
     *
     * @return array
     */
    protected function fullData(): array
    {
        return [
            'name' => "omarUpdateManger",
            'email' => 'omarUpdateManger@gmail.com',
            'password' => $password = $this->faker->password(8, 20),
            'password_confirmation' => $password,
            'restaurant' => 28,
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
        $this->successUpdate(105, $this->fullData());
    }
}
