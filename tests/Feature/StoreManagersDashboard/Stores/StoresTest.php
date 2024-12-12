<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Feature\ApiTestCaseUpdated;
use Illuminate\Support\Str;
use Illuminate\Http\Testing\File;

class StoresTest extends ApiTestCaseUpdated
{
    /**
     * Module route
     * 
     * @var string
     */
    protected $route = '/storeManagers/my-store';

    /**
     * Set base full accurate data
     * This includes required and optional data
     * 
     * @return array
     */
    protected function fullData(): array
    {
        return [
            'logo' => File::create('img1.png', 50),
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
            'id' => 'integer',
            // 'name.*.localeCode' => 'string',
            // 'name.*.text' => 'string',
            // 'description.*.localeCode' => 'string',
            // 'description.*.text' => 'string',
            'commercialRecordId' => 'string',
            'logo' => 'string', 
            'commercialRecordImage' => 'string',
            'location' => 'array',
            'shippingMethods' => 'array'
        ];
    }

    /**
     * Test Get Seller's Store
     */
    public function _testGetMyStore()
    {
        $this->successFoundRecord($this->route, 'record.', [
            'Authorization' => 'Bearer VnJUBgs3MOfynvKKJkxWCwbe9KobGjfrWntMVaeUNDTurUT5PQFClOKniTZPpvZkaEIGdJh5raaBBOxbGcsp2464Uf8ieVBK'
        ], $this->recordShape());
    }

    /**
     * Test Update Seller's Store
     */
    public function _testUpdateMyStore()
    {
        $response = $this->post($this->route, $this->fullData(), ['Authorization' => 'Bearer VnJUBgs3MOfynvKKJkxWCwbe9KobGjfrWntMVaeUNDTurUT5PQFClOKniTZPpvZkaEIGdJh5raaBBOxbGcsp2464Uf8ieVBK']);
        $response->assertStatus(200);
    }
}
