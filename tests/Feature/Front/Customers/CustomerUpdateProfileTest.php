<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Feature\ApiTestCaseUpdated;
use Illuminate\Support\Str;

class CustomerUpdateProfileTest extends ApiTestCaseUpdated
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
            "firstName" => "Tasneem",
            "lastName" => "Test",
            "healthInfo" => [
                "length" => "155",
                "weight" => "60",
                "age" => "25",
                "gender" => "female",
                "fatRatio" => "20",
                "targetWeight" => "50"
            ],
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
            "firstName" => "string",
            "lastName" => "string",
            "email" => "string",
            "phoneNumber" => "string",
            "accessToken" => "string",
            "id" => "integer",
            "totalNotifications" => "integer",
            "totalOrders" => "integer",
            "rewardPoint" => "integer",
            "rewardPointWithdraw" => "integer",
            "rewardPointDeposit" => "integer",
            "favoritesCount" => "integer",
            "totalRefusedReceive" => "integer",
            "walletBalance" => "integer",
            "totalOrdersPurchases" => "integer",
            "published" => "boolean",
            "isVerified" => "boolean",
            "healthInfo.length" => "string",
            "healthInfo.weight" => "string",
            "healthInfo.age" => "string",
            "healthInfo.gender" => "string",
            "healthInfo.fatRatio" => "string",
            "healthInfo.targetWeight" => "string",
            "cart" => "array",
            "cartSubscription" => "array",
            "createdAt.format" => "string",
            "createdAt.timestamp" => "integer",
            "createdAt.text" => "string",
            "createdAt.humanTime" => "string",
            // "birthDate" => "string"
        ];
    }

    /**
     * Test Get Customer's Profile Info
     */
    public function testGetProfile()
    {
        $this->successFoundRecord('', 'customer.', ['Authorization' => 'Bearer juqquhVDJ4XE2zbKwjALDqJ3kaXMoej8OaVCtgqoY52YGNJH9eXVM4ZLD0xUvPnMvPBk45kHHlTX3hrgdCNv4cPUOPgcxjgq'], $this->recordShape());
    }

    /**
     * Test unauthorized
     */
    public function testUnauthorized()
    {
        $response = $this->get($this->route);

        $response->assertStatus(401);
    }

    /**
     * Test Successful Update
     */
    public function testSuccessfulUpdate()
    {
        $response = $this->post($this->route, $this->fullData(), ['Authorization' => 'Bearer juqquhVDJ4XE2zbKwjALDqJ3kaXMoej8OaVCtgqoY52YGNJH9eXVM4ZLD0xUvPnMvPBk45kHHlTX3hrgdCNv4cPUOPgcxjgq']);

        $response->assertStatus(200);
    }

    /**
     * Test Some Validation Rules
     */
    public function testValidation()
    {
        // Test Minimum Rule
        $this->assertFailCreate($this->fullDataReplace(['firstName' => 'x', 'lastName' => 'y']), ['firstName', 'lastName'], false,
        ['Authorization' => 'Bearer juqquhVDJ4XE2zbKwjALDqJ3kaXMoej8OaVCtgqoY52YGNJH9eXVM4ZLD0xUvPnMvPBk45kHHlTX3hrgdCNv4cPUOPgcxjgq']);

        // Test Short Password
        $this->assertFailCreate($this->fullDataReplace(['password' => '1111', 'password_confirmation' => '1111']), ['password'], false,
        ['Authorization' => 'Bearer juqquhVDJ4XE2zbKwjALDqJ3kaXMoej8OaVCtgqoY52YGNJH9eXVM4ZLD0xUvPnMvPBk45kHHlTX3hrgdCNv4cPUOPgcxjgq']);

        // Test Password Not Confirmed
        $this->assertFailCreate($this->fullDataReplace(['password' => '123123123']), ['password'], false,
        ['Authorization' => 'Bearer juqquhVDJ4XE2zbKwjALDqJ3kaXMoej8OaVCtgqoY52YGNJH9eXVM4ZLD0xUvPnMvPBk45kHHlTX3hrgdCNv4cPUOPgcxjgq']);
    }
}
