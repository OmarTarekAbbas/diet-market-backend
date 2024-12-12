<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\ApiTestCase;

class CartTest extends ApiTestCase
{
    /**
     * Module route
     * 
     * @var string
     */
    protected $route = '/cart';

    /**
     * Add to cart Failure Because Of Required Options
     */
    public function _testNoProductOptions()
    {
        $response = $this->post($this->route, [
            'item' => 10,
            'quantity' => 2,
            'subscription' => 0,
            'type' => 'products'
        ]);
        $response->assertStatus(400);
    }

    /**
     * Successful Add To Cart
     */
    public function _testSuccessfulAddToCart()
    {
        $response = $this->post($this->route, [
            'item' => 10,
            'quantity' => 2,
            'subscription' => 0,
            'type' => 'products',
            'options' => [
                [
                    'id' => 1,
                    'values' => [10]
                ]
            ]
        ]);
        $response->assertStatus(200);
    }

    /**
     * Add to cart Failure No Item 
     */
    public function _testFailure()
    {
        $response = $this->post($this->route, [
            'quantity' => 2,
            'subscription' => 0,
            'type' => 'products'
        ]);
        $response->assertStatus(400);
    }

    /**
     * Add to cart Failure No Price For Product
     */
    public function _testFailureNoPrice()
    {
        $response = $this->post($this->route, [
            'item' => 4,
            'quantity' => 2,
            'subscription' => 0,
            'type' => 'food'
        ]);
        $response->assertStatus(400);
    }

    /**
     * Add to cart Failure No Price For Product
     */
    public function _testFailureProductNotFound()
    {
        $response = $this->post($this->route, [
            'item' => 9,
            'quantity' => 2,
            'subscription' => 0,
            'type' => 'products'
        ]);
        $response->assertStatus(400);
    }

    /**
     * Test Remove Cart Item
     */
    public function _testRemoveCartItem()
    {
        $response = $this->delete($this->route . '/' . 4 . '?type=products');

        $response->assertStatus(200);
    }

    /**
     * Test Failed Remove Cart Item Because No Type Specified
     */
    public function testFailedRemoveCartItem()
    {
        $response = $this->delete($this->route . '/' . 4);

        $response->assertStatus(400);
    }
}
