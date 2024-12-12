<?php

namespace App\Modules\Sizes\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;

class SizesController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'sizes';

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
     *List All Size
     * @return
     */
    public function sizes(Request $request)
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
     *crate for Size
     * @return array
     */
    public function create(Request $request)
    {
        $validator = $this->validatorForm($request);

        if (!$validator->passes()) {
            return $this->badRequest($validator->errors());
        }

        return $this->success([
            'record' => $this->repository->create($request),
        ]);
    }

    /**
     * Method update
     *
     * @param Request $request
     * update for Size
     * @return array
     */
    public function update($id, Request $request)
    {
        if ($this->repository->get($id)) {
            $updateSection = $this->repository->update($id, $request);

            return $this->success([
                'record' => $updateSection,
            ]);
        }

        return $this->badRequest(trans('errors.notFound'));
    }

    /**
     * Method destroy
     *
     * @param $id $id
     * @param Request $request
     *delete Size
     * @return
     */
    public function destroy($id, Request $request)
    {
        if ($this->repository->get($id)) {
            $this->repository->delete($id);

            return $this->success();
        }

        return $this->badRequest(trans('errors.notFound'));
    }

    /**
     * Method validatorForm
     *
     * @param $request $request
     * validator Form
     * @return object
     */
    public function validatorForm($request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|min:2',
            'price' => 'required',
        ]);
    }
}
