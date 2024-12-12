<?php

namespace App\Modules\Sections\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class SectionsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'sections';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [];

        return $this->success([
            'records' => $this->repository->list($options),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function show($id, Request $request)
    {
        return $this->success([
            'record' => $this->repository->get($id),
        ]);
    }

    /**
     * Method sections
     *
     * @param Request $request
     *
     * @return
     */
    public function sections(Request $request)
    {
        $options = [];

        return $this->success([
            'records' => $this->repository->list($options),
        ]);
    }

    /**
     * Method create
     *
     * @param Request $request
     *crate for restaurantManager/sections
     * @return array
     */
    public function create(Request $request)
    {
        return $this->success([
            'record' => $this->repository->create($request),
        ]);
    }

    /**
     * Method update
     *
     * @param Request $request
     * update for restaurantManager/sections
     * @return array
     */
    public function update($id, Request $request)
    {
        $updateSection = $this->repository->update($id, $request);

        return $this->success([
            'record' => $updateSection,
        ]);
    }

    /**
     * Method destroy
     *
     * @param $id $id
     * @param Request $request
     *delete
     * @return
     */
    public function destroy($id, Request $request)
    {
        $this->repository->delete($id);

        return $this->success();
    }
}
