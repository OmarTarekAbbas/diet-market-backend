<?php

namespace Tests\Feature;

use Illuminate\Http\Testing\File;

class AdminSingleClubManagerDataTest extends AdminApiTestCase
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
     * Method testList
     *
     * @return void
     */
    public function _testSuccessFoundRecord()
    {
        $this->successRecord(38);
    }

    /**
     * Check success found record
     *
     * @param   int $id
     * @return  void
     */
    protected function successRecord(int $id)
    {
        $this->successFoundRecord($id);
    }

}
