<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Feature\ApiTestCaseUpdated;
use Illuminate\Support\Str;

class SubmitTest extends ApiTestCaseUpdated
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/contact-us/submit';

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
            'email' => $this->faker->email,
            'phoneNumber' => '6666666',
            'subject' => Str::random(10),
            'type' => 'inquiry',
            'message' => Str::random(50),
            'department' => 'restaurants'
        ];
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
     * Test Success
     */
    public function testSuccess()
    {
        $this->successCreate('deliveryMan.', $this->fullData(), $this->recordShape());
    }

    /**
     * Test Validation Rules
     */
    public function testValidation()
    {
        // Test Required Rule
        $this->assertFailCreate([], ['name', 'email', 'subject', 'type', 'message', 'department']);

        // Test Type Values Validation
        $this->assertFailCreate($this->fullDataReplace(['type' => 'blah blah']), ['type']);

        // Test Department Values Validation
        $this->assertFailCreate($this->fullDataReplace(['department' => 'blah blah']), ['department']);
    }
}
