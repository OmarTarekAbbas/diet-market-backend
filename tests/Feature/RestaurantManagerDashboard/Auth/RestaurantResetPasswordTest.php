<?php

namespace Tests\Feature\RestaurantManagerDashboard;

use Illuminate\Http\Testing\File;

class RestaurantResetPasswordTest extends RestaurantApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/reset-password';

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
        $resetCode = '3719';
        return [
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
            'resetCode' => $resetCode,
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
            'resetCode' => 'integer',
        ];
    }

    /**
     * Method testSuccessResetPassword
     * test Success Reset Password
     * @return void
     */
    public function testSuccessResetPassword()
    {
        $this->successData($this->fullData(), $this->recordShape());
    }

    /**
     * Test Full Required Data Validation
     */
    public function testRequiredDataValidation()
    {
        $this->assertFailCreate([], ['email', 'password', 'password_confirmation', 'resetCode']);
    }

    /**
     * Test min length password
     */
    public function testMinPasswordLengthValidation()
    {
        $this->assertFailCreate($this->fullDataReplace(['password' => '9']), ['password']);
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
