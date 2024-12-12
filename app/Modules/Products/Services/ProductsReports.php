<?php

namespace App\Modules\Products\Services;

use DateTime;
use DateTimeZone;
use Carbon\Carbon;
use App\Modules\Products\Repositories\ProductsRepository;

class ProductsReports
{
    /**
     * Products Repository
     *
     * @var ProductsRepository
     */
    public $productsRepository;

    /**
     * Constructor
     *
     * @param ProductsRepository $productsRepository
     */
    public function __construct(ProductsRepository $productsRepository)
    {
        $this->productsRepository = $productsRepository;
    }

    /**
     * Get total orders based on the given options
     *
     * @param array $options
     * @return int
     */
    public function total(array $options = []): int
    {
        $query = $this->productsRepository->getQuery();

        if (!empty($options['from'])) {
            $query->where('createdAt', '>=', Carbon::parse($options['from']));
        }

        if (!empty($options['to'])) {
            $query->where('createdAt', '<=', Carbon::parse($options['to']));
        }

        if (!empty($options['totalViews'])) {
            $query->where('totalViews', '>', 0);
        }

        if (!empty($options['outOfStock'])) {
            $query->where('availableStock', '<', 0);
        }

        return $query->count();
    }

    /**
     * Get the week of the given day in the month
     *
     * @param int $dayNumber
     * @return int
     */
    public static function getDayWeekNumber(int $dayNumber): int
    {
        return $dayNumber % 7 == 0 ? $dayNumber / 7 : floor($dayNumber / 7) + 1;
    }

    /**
     * Set MongoDB Timezone to UTC
     *
     * @param Carbon $date
     * @return DateTime
     */
    public static function mongoTime(Carbon $date): Carbon
    {
        return $date;

        return $date->setTimezone(new DateTimeZone('UTC'));
    }

    /**
     * get product views
     *
     * @return mixed
     */
    public function getTotalViews()
    {
        $query = $this->productsRepository->getQuery();

        $totalViews = $query->sum('totalViews');

        $query = $query->where('totalViews', '>', 0)->select(['id', 'name', 'category', 'totalViews'])->get();

        return $query->map(function ($product) use ($totalViews) {
            $product['viewRate'] = round((($product['totalViews'] / $totalViews) * 100), 2);
            unset($product['_id']);

            return $product;
        });
    }

    /**
     * get product sales
     *
     * @return mixed
     */
    public function getTotalSales()
    {
        $query = $this->productsRepository->getQuery();

        $totalSales = $query->sum('sales');

        $query = $query->where('sales', '>', 0)->select(['id', 'name', 'category', 'sales'])->get();

        return $query->map(function ($product) use ($totalSales) {
            $product['saleRate'] = round((($product['totalViews'] / $totalSales) * 100), 2);
            unset($product['_id']);

            return $product;
        });
    }

    /**
     * get product sales
     *
     * @return mixed
     */
    public function getOutOfStock()
    {
        $query = $this->productsRepository->getQuery();

        return $query->where('availableStock', '<=', 0)->select(['id', 'name', 'category', 'sales', 'availableStock'])->get()->map(function ($product) {
            unset($product['_id']);

            return $product;
        });
    }
}
