<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Http\Testing\File;

class AdminDeleteMealsTest extends AdminApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/meals';

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
        $this->successDelete(23);
    }

    /**
     * Test Not Found record
     */
    public function testNotFoundRecord()
    {
        $this->successNotFoundRecord(1000);
    }
}
