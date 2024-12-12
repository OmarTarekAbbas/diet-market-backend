<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Http\Testing\File;

class AdminCreateRestaurantManagerTest extends AdminApiTestCase
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
            'name' => $this->faker->name,
            'email' => $this->faker->email,
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
        return [
            'name' => 'string',
            'email' => 'string',
        ];
    }

    /**
     * Method testSuccessCreate
     * test Success Create
     * @return void
     */
    public function _testSuccessCreate()
    {
        $this->successCreate($this->fullData(), $this->recordShape());
    }

    /**
     * Test Full Required Data Validation
     */
    public function _testRequiredDataValidation()
    {
        $this->assertFailCreate([], ['name', 'email']);
    }

    /**
     * Test min length password
     */
    public function _testMinPasswordLengthValidation()
    {
        $this->assertFailCreate($this->fullDataReplace(['password' => '9']), ['password']);
    }

    /**
     * Test Mis Matched Password
     */
    public function _testMisMatchedPasswordValidation()
    {
        $this->assertFailCreate($this->fullDataWith([
            'password' => 'misMatchingPassword-TEST',
            'password_confirmation' => 'misMatchingPassword',
        ]), ['password']);
    }
}
