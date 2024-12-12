<?php

namespace App\Modules\Products\Repositories;

use App\Modules\Products\Models\Product as Model;
use App\Modules\Products\Resources\ProductMeal as Resource;
use HZ\Illuminate\Mongez\{
    Contracts\Repositories\RepositoryInterface,
};

class ProductMealsRepository extends ProductsRepository implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'productMeals';

    /**
     * Resource class name
     *
     * @const string
     */
    const RESOURCE = Resource::class;

    /**
     * Determine wether to use pagination in the `list` method
     * if set null, it will depend on pagination configurations
     *
     * @const bool
     */
    const PAGINATE = false;

    /**
     * Number of items per page in pagination
     * If set to null, then it will taken from pagination configurations
     *
     * @const int|null
     */
    const ITEMS_PER_PAGE = null;

    // /**
    //  * Method bestSellerProductMeals
    //  *
    //  * @return void
    //  */
    // public function bestSellerProductMeals()
    // {
    //     // dd('sdsd');
    //     return $this->productMealsRepository->getQuery()->where('sales', '>', 0)->where('type', 'food')->get();
    //     // $this->productMealsRepository->wrapMany($this->productMealsRepository->getQuery()->where('sales', '>', 0)->where('type', 'food')->get());
    // }

    /**
     * Method updateProductMeals
     *
     * @param $nutritionSpecialist $nutritionSpecialist
     *
     * @return void
     */
    public function updateProductMeals($productMeals)
    {
        // dd($productMeals);
        Model::where('restaurant.id', $productMeals->id)->update([
            'restaurant' => $productMeals->sharedInfo(),
        ]);
    }

    /**
     * Method deleteProductMeals
     *
     * @param $productMeals $productMeals
     *
     * @return void
     */
    public function deleteProductMeals($productMeals)
    {
        Model::where('restaurant.id', $productMeals->id)->delete();
    }

    /**
     * Method onCreate
     *
     * @param $model $model
     * @param $request $request
     *
     * @return void
     */
    public function onCreate($model, $request)
    {
        $this->restaurantsRepository->createProduct($model, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function onUpdate($model, $request, $oldModel)
    {
        $this->favoritesRepository->updateProduct($model);
        $this->restaurantsRepository->UpdateProduct($model, $request, $oldModel);
    }

    /**
     * @param $model
     * @param $id
     * {@inheritDoc}
     */
    public function onDelete($model, $id)
    {
        $this->favoritesRepository->removeProduct($model);
        $this->restaurantsRepository->removeProduct($model);
    }

    public function isAvailableItemFood($items)
    {
        foreach ($items as $key => $item) {
            // dd((int)$item['product']['id']);
            $isAvailableItem = $this->productsRepository->get((int) $item['product']['id']);

            if ($isAvailableItem && $isAvailableItem->published == true) {
                return true;
            }
        }
    }
}
