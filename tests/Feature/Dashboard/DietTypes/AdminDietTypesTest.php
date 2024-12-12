<?php

namespace Tests\Feature\Dashboard\DietTypes;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\AdminApiTestCase;
use Illuminate\Support\Str;

class AdminDietTypesTest extends AdminApiTestCase
{
    /**
     * Module route
     * 
     * @var string
     */
    protected $route = '/diet-types';

    /**
     * Set base full accurate data
     * This includes required and optional data
     * 
     * @return array
     */
    protected function fullData(): array
    {
        return [
            'name' => Str::random(20), 
            'proteinRatio' => 40.1, 
            'carbohydrateRatio' => 15.5, 
            'fatRatio'=> 10.5, 
            'published' => 1
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
            'name' => 'string', 
            'proteinRatio' => 'double', 
            'carbohydrateRatio' => 'double', 
            'fatRatio' => 'double', 
            'published' => 'boolean', 
        ];
    }

    /**
     * Test Full Data Success Creation
     */
    public function _testSuccessCreate()
    {
        $this->successCreate($this->fullData(), $this->recordShape());
    }

    /**
     * Test Full Data Success Update
     */
    public function _testSuccessUpdate()
    {
        $this->successUpdate(2, $this->fullData());
    }

    /**
     * Test Failed Update , Item not Found
     */
    public function testFailedUpdate()
    {
        $response = $this->put($this->route . '/' . 700, $this->fullData());
        $response->assertStatus(404);
    }

    /**
     * Test Validation
     */
    public function testValidation()
    {
        $this->assertFailCreate([], ['name', 'proteinRatio', 'carbohydrateRatio', 'fatRatio', 'published']);
    }

    /**
     * Test Success Delete
     */
    public function _testSuccessDelete()
    {
        $this->successDelete(3);
    }

    /**
     * Test Not Found record
     */
    public function testNotFoundRecord()
    {
        $this->successNotFoundRecord(100);
    }
}
