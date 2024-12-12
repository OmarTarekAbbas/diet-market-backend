<?php

namespace Tests\Feature\Front\DietTypes;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\ApiTestCase;

class DietTypesTest extends ApiTestCase
{
    /**
     * Module route
     * 
     * @var string
     */
    protected $route = '/diet-types';

    public function testListDietTypes()
    {
        $response = $this->get($this->route);
        $response->assertStatus(200);
    }
}
