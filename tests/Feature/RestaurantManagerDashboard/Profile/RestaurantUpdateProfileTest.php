<?php

namespace Tests\Feature\RestaurantManagerDashboard;

use Illuminate\Http\Testing\File;

class RestaurantUpdateProfileTest extends RestaurantApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/me';

    /**
     * Set base full accurate data
     * This includes required and optional data
     *
     * @return array
     */
    protected function fullData(): array
    {
        return [
            'name' => 'omarTarekAbbass',
            'email' => 'omart8703@gmail.com',
            'oldPassword' => '123456789',
            'password' => $password = '123456789',
            'password_confirmation' => $password,
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
        $this->assertFailCreate([], ['name', 'email']);
    }

    /**
     * Test min length password
     */
    public function testMinPasswordLengthValidation()
    {
        $this->assertFailCreate($this->fullDataReplace(['password' => '6']), ['password']);
    }

    /**
     * Test Mis Matched Password
     */
    public function testMisMatchedPasswordValidation()
    {
        $this->assertFailCreate($this->fullDataWith([
            'password' => 'misMatchingPassword-TEST',
            'password_confirmation' => 'misMatchingPassword',
        ]), ['password']);
    }
}
