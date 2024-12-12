<?php

namespace Tests\Feature\RestaurantManagerDashboard;

use Illuminate\Http\Testing\File;

class RestaurantForgetpasswordTest extends RestaurantApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/forget-password';

    /**
     * Set base full accurate data
     * This includes required and optional data
     *
     * @return array
     */
    protected function fullData(): array
    {
        $email = 'omart8703@gmail.com';
        return [
            'email' => $email,
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
        ];
    }

    /**
     * Method testSuccessForgetPassword
     * test Success Forget Password
     * @return void
     */
    public function testSuccessForgetPassword()
    {
        $this->successData($this->fullData(), $this->recordShape());
    }

    /**
     * Test Full Required Data Validation
     */
    public function testRequiredDataValidation()
    {
        $this->assertFailCreate([], ['email']);
    }
}
