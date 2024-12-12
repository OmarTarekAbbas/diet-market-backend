<?php

namespace App\Modules\Meals\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;

class MealsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'meals';

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
     * Method meals
     *
     * @param Request $request
     *
     * @return
     */
    public function meals(Request $request)
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
     *crate for restaurantManager/meals
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
     * update for restaurantManager/meals
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
     *delete
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
            'name' => 'required',
            'protein' => 'required',
            'carbohydrates' => 'required',
            'fat' => 'required',
            'categories' => 'required',
            'image' => 'required',
        ]);
    }
}