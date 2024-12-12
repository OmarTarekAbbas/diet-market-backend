<?php

namespace App\Modules\Favorites\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class FavoritesController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'favorites';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'customer' => user()->id,
        ];

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'records' => $this->repository->list($options),
            'paginationInfo' => $this->repository->getPaginateInfo(),
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
     * @param Request $request
     * @return \Illuminate\Http\Response|string
     */
    public function addToFavorites(Request $request)
    {
        if (!$request->has('productId')) {
            return $this->badRequest(trans('missingProductId'));
        }

        if (!$this->productsRepository->has((int) $request->productId)) {
            return $this->badRequest(trans('products.notFound'));
        }

        if ($this->repository->existsInFavorites((int) $request->productId)) {
            return $this->badRequest(trans('favorite.productAlreadyInFavorite'));
        }

        $this->repository->addToFavorites($request);

        return $this->index($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|string
     */
    public function removeFromFavorites(Request $request)
    {
        if (!$request->has('productId')) {
            return $this->badRequest(trans('missingProductId'));
        }

        if (!$this->productsRepository->has((int) $request->productId)) {
            return $this->badRequest(trans('products.notFound'));
        }

        if (!$this->repository->existsInFavorites((int) $request->productId)) {
            return $this->badRequest(trans('favorite.productAlreadyNotInFavorite'));
        }

        $this->repository->removeFromFavorites($request);

        return $this->index($request);
    }
}
