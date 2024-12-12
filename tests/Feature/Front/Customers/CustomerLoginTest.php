<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Feature\ApiTestCaseUpdated;

class CustomerLoginTest extends ApiTestCaseUpdated
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
        return [];
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
     * Test Successful Login
     */
    public function testSuccessfulLogin()
    {
        $response = $this->post($this->route, [
            'emailOrPhone' => 'test@rowaad.net',
            'password' => '123123123'
        ], [
            'Authorization' => 'Key ASFDA23RWFEGHJKI87654EDFBNMJKI76543EWDFGHJ'
        ]);
        $response->assertStatus(200);
    }

    /**
     * Test Wrong Mail
     */
    public function testWrongMail()
    {
        $this->assertInvalidData([
            'emailOrPhone' => 'tasneem@rowaad.net',
            'password' => '123123123'
        ], ['error'], true);
    }
}
