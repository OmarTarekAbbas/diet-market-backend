<?php

namespace Tests\Feature;

use Illuminate\Http\Testing\File;

class AdminDeleteClubManagerTest extends AdminApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/clubmanagers';

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
    public function _testSuccessDelete()
    {
        $this->successDelete(39);
    }

    /**
     * Test Not Found record
     */
    public function _testNotFoundRecord()
    {
        $this->successNotFoundRecord(39);
    }
}
