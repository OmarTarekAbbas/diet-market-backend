<?php

namespace Tests\Feature\RestaurantManagerDashboard;

use Illuminate\Http\Testing\File;

class RestaurantLoginTest extends RestaurantApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/login';

    /**
     * Set base full accurate data
     * This includes required and optional data
     *
     * @return array
     */
    protected function fullData(): array
    {
        $email = 'omart8703@gmail.com';
        $password = '123456789';
        return [
            'email' => $email,
            'password' => $password,
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
            'email' => 'string',
            'password' => 'string',
        ];
    }

    /**
     * Method testSuccessLogin
     * test Success Login
     * @return void
     */
    public function testSuccessLogin()
    {
        $this->successData($this->fullData(), $this->recordShape());
    }

    /**
     * Test Full Required Data Validation
     */
    public function testRequiredDataValidation()
    {
        $this->assertFailCreate([], ['email', 'password']);
    }

    /**
     * Test min length password
     */
    public function testMinPasswordLengthValidation()
    {
        $this->assertFailCreate($this->fullDataReplace(['password' => '9']), ['password']);
    }
}
