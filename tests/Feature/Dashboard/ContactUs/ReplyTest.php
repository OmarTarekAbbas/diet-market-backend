<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Feature\AdminApiTestCase;
use Illuminate\Support\Str;

class ReplyTest extends AdminApiTestCase
{
    /**
     * Module route
     * 
     * @var string
     */
    protected $route = '/contact-us';

    /**
     * Set base full accurate data
     * This includes required and optional data
     * 
     * @return array
     */
    protected function fullData(): array
    {
        return [
            'reply' => Str::random(50), 
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
            'email' => 'string',
            'subject' => 'string',
            'type' => 'string',
            'message' => 'string',
            'department' => 'string',
        ];
    }

    /**
     * Test Get Contact Us Messages
     */
    public function testSuccessfullyGetAllMessages()
    {
        $this->successFoundRecord();
    }

    /**
     * Test Get A Contact Us Message
     */
    public function testSuccessfullyGetMessage()
    {
        $this->successFoundRecord(1);
    }

    /**
     * Test Get A Not Found Contact Us Message
     */
    public function testSuccessfullyNotFound()
    {
        $this->successNotFoundRecord(100);
    }

    /**
     * Test Reply to Contact Us Message
     */
    public function _testSuccessfulReply()
    {
        $response = $this->post($this->route . '/1/reply', $this->fullData());

        $response->assertStatus(200);
    }

    /**
     * Test Failed Reply
     */
    public function testFailedReply()
    {
        $response = $this->post($this->route . '/1/reply', $this->fullDataExcept(['reply']));

        $response->assertStatus(400);
    }
}
