<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\ApiTestCaseUpdated;
use Illuminate\Http\Testing\File;

class ProductsTest extends ApiTestCaseUpdated
{
    /**
     * Module route
     * 
     * @var string
     */
    protected $route = '/products';

    /**
     * Set base full accurate data
     * This includes required and optional data
     * 
     * @return array
     */
    protected function fullData(): array
    {
        $fullData = [];

        $fullData['name'][0]['text'] = 'منتج جديد';
        $fullData['name'][0]['localeCode'] = 'ar';
        $fullData['name'][1]['text'] = 'New Product';
        $fullData['name'][1]['localeCode'] = 'en';
        $fullData['description'][0]['text'] = 'وصف منتج جديد';
        $fullData['description'][0]['localeCode'] = 'ar';
        $fullData['description'][1]['text'] = 'New Product description';
        $fullData['description'][1]['localeCode'] = 'en';
        $fullData['nutritionalValue']['protein'] = '100';
        $fullData['nutritionalValue']['fat'] = '1000';
        $fullData['nutritionalValue']['carbs'] = '500';
        $fullData['category'] = 1;
        $fullData['quantity'] = 1000;
        $fullData['rewardPoints'] = 50;
        $fullData['purchaseRewardPoints'] = 300;
        $fullData['price'] = 120;
        $fullData['finalPrice'] = 120;
        $fullData['availableStock'] = 100;
        $fullData['maxQuantity'] = 4;
        $fullData['minQuantity'] = 1;
        $fullData['published'] = 1;
        $fullData['imported'] = 1;
        $fullData['images'][0] = File::create('img1.png', 50);

        $fullData['options'][0]['optionId'] = 2;
        $fullData['options'][0]['type'] = 'type';
        $fullData['options'][0]['required'] = 1;
        $fullData['options'][0]['values'][0]['id'] = 10;
        $fullData['options'][0]['values'][0]['price'] = 10;
        $fullData['options'][0]['values'][1]['id'] = 11;
        $fullData['options'][0]['values'][1]['price'] = 10;

        return $fullData;
    }

    /**
     * Set what response record is expected to be returned
     * 
     * @return array
     */
    protected function recordShape(): array
    {
        $recordShape = [];

        // $recordShape['name'][0]['text'] = 'string';
        // $recordShape['name'][0]['localeCode'] = 'string';
        // $recordShape['name'][1]['text'] = 'string';
        // $recordShape['name'][1]['localeCode'] = 'string';
        // $recordShape['description'][0]['text'] = 'string';
        // $recordShape['description'][0]['localeCode'] = 'string';
        // $recordShape['description'][1]['text'] = 'string';
        // $recordShape['description'][1]['localeCode'] = 'string';
        $recordShape['name'] = 'array';
        $recordShape['description'] = 'array';
        $recordShape['nutritionalValue']['protein'] = 'string';
        $recordShape['nutritionalValue']['fat'] = 'string';
        $recordShape['nutritionalValue']['carbs'] = 'string';
        $recordShape['category'] = 'array';
        $recordShape['quantity'] = 'integer';
        $recordShape['rewardPoints'] = 'integer';
        $recordShape['purchaseRewardPoints'] = 'integer';
        $recordShape['price'] = 'integer';
        $recordShape['finalPrice'] = 'integer';
        $recordShape['availableStock'] = 'integer';
        $recordShape['maxQuantity'] = 'integer';
        $recordShape['minQuantity'] = 'integer';
        $recordShape['published'] = 'boolean';
        $recordShape['imported'] = 'boolean';

        return $recordShape;
    }

    /**
     * Test Full Data Success Creation
     */
    public function _testSuccessCreate()
    {
        $this->successCreate('record.', $this->fullDataExcept(['options']), $this->recordShape());
    }

    /**
     * Test Full Required Data Validation
     */
    public function _testRequiredDataValidation()
    {
        $this->assertFailCreate($this->fullDataExcept(['name']), ['name']);
    }

    /**
     * Test Success Delete
     */
    public function _testSuccessDelete()
    {
        $this->successDelete(9);
    }

    /**
     * Test Not Found record
     */
    public function _testNotFoundRecord()
    {
        $this->successNotFoundRecord(9);
    }

    /**
     * Test Full Data Success Creation With Options
     */
    public function _testSuccessCreateWithOptions()
    {
        $this->successCreate('record.', $this->fullData(), $this->recordShape());
    }
}
