<?php

namespace Tests\Feature\Front;

use Illuminate\Http\Testing\File;

class FrontCreateServiceProviderTest extends FrontApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/serviceProvider';

    /**
     * Set base full accurate data
     * This includes required and optional data
     *
     * @return array
     */
    protected function fullData(): array
    {
        return [
            'tradeName' => [
                [
                    'text' => $this->faker->name,
                    'localCode' => 'en',
                ],
                [
                    'text' => $this->faker->name,
                    'localCode' => 'ar',
                ]
            ],
            'address' => [
                [
                    'text' => $this->faker->address,
                    'localCode' => 'en',
                ],
                [
                    'text' => $this->faker->address,
                    'localCode' => 'ar',
                ]
            ],
            'commercialImage' => File::create('img1.png', 100),
            'country' => '1',
            'city' => '1',
            'serviceType' => '1',
            'commercialNumber' => '12345678',
            'published' => '1',
            'firstName' => 'OmarTarekTest',
            'lastName' => 'OmarTarekTest',
            'email' => 'emailt123@gmail.com',
            'phoneNumber' => '01123656796',
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
            'tradeName' => 'array',
            'address' => 'array',
            'commercialImage' => 'string',
            'country' => 'array',
            'city' => 'array',
            'serviceType' => 'integer',
            'commercialNumber' => 'string',
            'published' => 'boolean',
            'firstName' => 'string',
            'lastName' => 'string',
            'email' => 'string',
            'phoneNumber' => 'string',
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

}
