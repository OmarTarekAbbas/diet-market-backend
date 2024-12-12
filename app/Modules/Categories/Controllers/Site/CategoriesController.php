<?php

namespace App\Modules\Categories\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;

class CategoriesController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    const REPOSITORY_NAME = 'categories';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = $request->all();
        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'records' => $this->repository->listPublished($options),
            // 'paginationInfo' => $this->repository->getPaginateInfo(),
            'returnOrderStatus' => $this->settingsRepository->getSetting('ReturnedOrder', 'returnSystemStatus'),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function show($id)
    {
        $category = $this->repository->getPublished($id);

        if (!$category) {
            return $this->notFound(trans('errors.notFound'));
        }

        return $this->success([
            'record' => $category,
        ]);
    }

    /**
     * Method categories
     *
     * @param Request $request
     *
     * @return
     */
    public function categories(Request $request)
    {
        $options = [];

        return $this->success([
            'records' => $this->repository->listPublished($options),
        ]);
    }

    /**
     * Method create
     *
     * @param Request $request
     *crate for restaurantManager/categories
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
     * update for restaurantManager/categories
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
     *delete categories
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
     * Method showCategoriesRestaurant
     *
     * @param $id $id
     * @param Request $request
     * show Categorie sRestaurant
     * @return void
     */
    public function showCategoriesRestaurant($id, Request $request)
    {
        $category = $this->repository->get($id);

        if (!$category) {
            return $this->notFound(trans('errors.notFound'));
        }

        return $this->success([
            'record' => $category,
            'Items' => $this->repository->showItemsForCategoryRepository($id),
        ]);
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
            'description' => 'required',
            'type' => 'required|in:food,products',
            // 'restaurant' => 'required',
        ]);
    }
}
