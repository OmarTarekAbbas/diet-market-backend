<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Feature\ApiTestCaseUpdated;
use Illuminate\Support\Str;
use Illuminate\Http\Testing\File;

class DeliveryManRegisterTest extends ApiTestCaseUpdated
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/deliveryMen/register';

    /**
     * Set base full accurate data
     * This includes required and optional data
     *
     * @return array
     */
    protected function fullData(): array
    {
        return [
            'firstName' => $this->faker->name,
            'lastName' => $this->faker->name,
            'password' => '123123123',
            'phoneNumber' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'idNumber' => $this->faker->numerify(),
            'nationality' => $this->faker->country(),
            'transportation' => [
                'type' => 'car',
                'brand' => 'mercedes',
                'model' => 'benz',
                'releaseYear' => '2012',
            ],
            'bankAccountNumber' => $this->faker->numerify(),
            'idImage' => File::create('img1.png', 50),
            'driverLicense' => File::create('img2.png', 50),
            'vehicleFront' => File::create('img3.png', 50),
            'vehicleBack' => File::create('img4.png', 50),
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
            'firstName' => 'string',
            'lastName' => 'string',
            'password' => 'string',
            'phoneNumber' => 'string',
            'email' => 'string',
            'idNumber' => 'string',
            'nationality' => 'string',
            'transportation.type' => 'string',
            'transportation.brand' => 'string',
            'transportation.model' => 'string',
            'transportation.releaseYear' => 'string',
            'bankAccountNumber' => 'string',
            'idImage' => 'string',
            'driverLicense' => 'string',
            'vehicleFront' => 'string',
            'vehicleBack' => 'string',
        ];
    }

    /**
     * Test Successful Creation
     */
    public function testSuccessfulCreation()
    {
        $this->successCreate('deliveryMan.', $this->fullData(), $this->recordShape());
    }

    /**
     * Test Required firstName
     */
    public function testRequiredFirstname()
    {
        $this->assertFailCreate($this->fullDataExcept(['firstName']), ['firstName']);
    }

    /**
     * Test Unique Email Rule
     */
    public function testUniqueEmailRule()
    {
        $this->assertFailCreate($this->fullDataReplace(['email' => 'test@rowaad.net']), ['email']);
    }

    /**
     * Test Some Validation Rules
     */
    public function testValidation()
    {
        // Test Minimum Rule
        $this->assertFailCreate($this->fullDataReplace(['firstName' => 'x', 'lastName' => 'y']), ['firstName', 'lastName']);

        // Test Image Rule
        $this->assertFailCreate($this->fullDataReplace(['idImage' => 'blah blah']), ['idImage']);
    }
}
