<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Feature\ApiTestCaseUpdated;
use Illuminate\Support\Str;

class OptionsTest extends ApiTestCaseUpdated
{
    /**
     * Module route
     * 
     * @var string
     */
    protected $route = '/storeManagers/options';

    /**
     * Set base full accurate data
     * This includes required and optional data
     * 
     * @return array
     */
    protected function fullData(): array
    {
        $fullData = [
            'type' => 'radio',
            'name' => [
                [
                    'localeCode' => 'en',
                    'text' => Str::random(10),
                ],
                [
                    'localeCode' => 'ar',
                    'text' => 'داتا',
                ]
            ]
        ];

        $fullData['values'] = [];
        $fullData['values'][0]['sortOrder'] = 1;
        $fullData['values'][0]['name'][0]['text'] = 'قيمة 1';
        $fullData['values'][0]['name'][0]['localeCode'] = 'ar';
        $fullData['values'][0]['name'][1]['text'] = 'value 1';
        $fullData['values'][0]['name'][1]['localeCode'] = 'en';

        return $fullData;
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
            'type' => 'string',
            'values' => 'array'
        ];
    }

    /**
     * Test Successful Creation of Options
     */
    public function _testSuccessfulCreation()
    {
        $this->successCreate('record.', $this->fullDataExcept(['values']), $this->recordShape());
    }

    /**
     * Test Successful Creation of Options
     */
    public function _testSuccessfulCreationWithOptionValues()
    {
        $this->successCreate('record.', $this->fullData(), $this->recordShape());
    }

    /**
     * Required Data
     */
    public function _testRequiredValidation()
    {
        $this->assertFailCreate([], ['name']);
    }

    /**
     * Test Successful Update of Options
     */
    public function _testSuccessfulUpdate()
    {
        $this->successUpdate(12, $this->fullData());
    }

    /**
     * Test Successful Delete of an Option
     */
    public function _testSuccessDelete()
    {
        $this->successDelete(14);
    }

    /**
     * Test List Options With Locale Code
     */
    public function _testListWithLocale()
    {
        $this->successFoundRecord('/options/13', ['LOCALE-CODE' => 'ar'], $this->recordShape());
    }
}
