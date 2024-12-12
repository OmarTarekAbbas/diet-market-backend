<?php


namespace Tests\Feature;

use Illuminate\Http\Testing\File;

class AdminUpdateClubManagerTest extends AdminApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/clubmanagers';

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
            'password' => $password = $this->faker->password(8, 20),
            'password_confirmation' => $password,
            'phone' => 'phoneNumber',
            'published' => true,
            'email' => $this->faker->email,
            'club' => 45,
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
            'id' => 'integer',
            'name' => 'string',
            'email' => 'string',
            'phone' => 'string',
        ];
    }



    /**
     * Method testSuccessUpdate
     * test Success Update
     * @return void
     */
    public function _testSuccessUpdate()
    {
        $this->successUpdate(2, $this->fullData(), $this->recordShape());
    }

    /**
     * Test email validation pattern
     */
    public function _testEmailPatternValidation()
    {
        $this->assertFailCreate($this->fullDataReplace(['email' => 'un-well-formatted-mail']), ['email']);
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
