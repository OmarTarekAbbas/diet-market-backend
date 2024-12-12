<?php

namespace Tests\Feature\RestaurantManagerDashboard;

use Illuminate\Http\Testing\File;

class RestaurantDeleteSizesTest extends RestaurantApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/size';

    /**
     * Set base full accurate data
     * This includes required and optional data
     *
     * @return array
     */
    protected function fullData(): array
    {
        return [];
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
     * Test Success Delete
     */
    public function testSuccessDelete()
    {
        $this->successDelete(29);
    }
}
