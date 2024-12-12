<?php

namespace App\Modules\Orders\Services;

use DateTime;
use DateTimeZone;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Modules\Orders\Repositories\OrdersRepository;
use App\Modules\Coupons\Repositories\CouponsRepository;
use App\Modules\ShippingMethods\Repositories\ShippingMethodsRepository;

class OrdersReports
{
    /**
     * Orders Repository
     *
     * @var OrdersRepository
     */
    public $ordersRepository;

    /**
     * @var ShippingMethodsRepository
     */
    private $shippingMethodsRepository;

    /**
     * @var CouponsRepository
     */
    private $couponsRepository;

    /**
     * @var \HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface|\HZ\Illuminate\Mongez\Contracts\RepositoryInterface
     */
    private $customersRepository;

    /**
     * Constructor
     *
     * @param OrdersRepository $ordersRepository
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    public function __construct(OrdersRepository $ordersRepository)
    {
        $this->ordersRepository = $ordersRepository;
        $this->shippingMethodsRepository = repo('shippingMethods');
        $this->couponsRepository = repo('coupons');
        $this->customersRepository = repo('customers');
        $this->storesRepository = repo('stores');
        $this->restaurantsRepository = repo('restaurants');
        $this->clubsRepository = repo('clubs');
        $this->nutritionSpecialistsRepository = repo('nutritionSpecialists');
        $this->settingsRepository = repo('settings');
    }

    /**
     * Get total orders based on the given options
     *
     * @param array $options
     * @return int
     */
    public function total(array $options = []): int
    {
        $query = $this->ordersRepository->getQuery();

        if (!empty($options['from'])) {
            $query->where('createdAt', '>=', Carbon::parse($options['from']));
        }

        if (!empty($options['to'])) {
            $query->where('createdAt', '<=', Carbon::parse($options['to']));
        }

        if (!empty($options['status'])) {
            $query->where('status', $options['status']);
        }

        return $query->count();
    }

    /**
     * Method totalRestaurants
     *
     * @param array $options
     *
     * @return int
     */
    public function totalRestaurants(array $options = []): int
    {
        $query = $this->ordersRepository->getQuery();

        if (!empty($options['from'])) {
            $query->where('createdAt', '>=', Carbon::parse($options['from']));
        }

        if (!empty($options['to'])) {
            $query->where('createdAt', '<=', Carbon::parse($options['to']));
        }

        if (!empty($options['status'])) {
            $query->where('status', $options['status']);
        }

        $query->where('productsType', 'food');

        return $query->count();
    }

    /**
     * Method totalStore
     *
     * @param array $options
     *
     * @return int
     */
    public function totalStore(array $options = []): int
    {
        $query = $this->ordersRepository->getQuery();

        if (!empty($options['from'])) {
            $query->where('createdAt', '>=', Carbon::parse($options['from']));
        }

        if (!empty($options['to'])) {
            $query->where('createdAt', '<=', Carbon::parse($options['to']));
        }

        if (!empty($options['status'])) {
            $query->where('status', $options['status']);
        }

        $query->where('productsType', 'products');

        return $query->count();
    }

    public function totalClubs(array $options = []): int
    {
        $query = $this->ordersRepository->getQuery();

        if (!empty($options['from'])) {
            $query->where('createdAt', '>=', Carbon::parse($options['from']));
        }

        if (!empty($options['to'])) {
            $query->where('createdAt', '<=', Carbon::parse($options['to']));
        }

        if (!empty($options['status'])) {
            $query->where('status', $options['status']);
        }

        $query->where('productsType', 'clubs');

        return $query->count();
    }

    /**
     * Get order sales for the given week number in the given year number in the 7 days
     *
     * @param string $date
     * @return array
     */
    public function weeklySales($date)
    {
        $firstDayOfTheWeek = Carbon::parse($date)->startOfWeek(Carbon::SATURDAY);
        $lastDayOfTheWeek = $firstDayOfTheWeek->clone()->addDays(7);

        return $this->sales([
            'from' => $firstDayOfTheWeek,
            'to' => $lastDayOfTheWeek,
        ]);
    }

    /**
     * Get order sales for the given year and month
     *
     * @param int $year
     * @param int $month
     * @return array
     */
    public function monthSales($year, $month)
    {
        $date = Carbon::parse("01-{$month}-{$year}");

        return $this->sales([
            'from' => $date,
            'to' => $date->copy()->lastOfMonth(),
            // 'groupBy' => 'week',
        ]);

        $weeks = [];

        for ($week = 1; $week <= 4; $week++) {
            $weeks[] = $this->sales([
                'from' => $date->copy(),
                'to' => $date->addDays(7),
            ]);
        }

        if ($date->copy()->endOfMonth()->format('d') > 28) {
            $weeks[] = $this->sales([
                'from' => $date->addDays(1),
                'to' => $date->lastOfMonth(),
            ]);
        }

        return $weeks;
    }

    /**
     * Get order sales for the given year in 12 months
     *
     * @param int $year
     * @return array
     */
    public function yearSales($year)
    {
        $date = Carbon::parse("01-01-{$year}");

        return $this->sales([
            'from' => $date,
            'to' => $date->copy()->lastOfYear(),
            'groupBy' => 'month',
        ]);

        $weeks = [];

        for ($week = 1; $week <= 4; $week++) {
            $weeks[] = $this->sales([
                'from' => $date->copy(),
                'to' => $date->addDays(7),
            ]);
        }

        if ($date->copy()->endOfMonth()->format('d') > 28) {
            $weeks[] = $this->sales([
                'from' => $date->addDays(1),
                'to' => $date->lastOfMonth(),
            ]);
        }

        return $weeks;
    }

    /**
     * Get sales reports grouped by day for the given options
     *
     * @param array $options
     * @return array
     */
    public function sales(array $options)
    {
        $from = Carbon::parse($options['from']);
        $to = Carbon::parse($options['to']);
        $groupBy = $options['groupBy'] ?? 'date';

        // dump($firstDayOfTheWeek);
        // dump($lastDayOfTheWeek);

        // $days = $this->ordersRepository->aggregate()
        //     ->where('status', OrdersRepository::COMPLETED_STATUS)
        //     ->where('createdAt', '>=', ($firstDayOfTheWeek))
        //     ->where('createdAt', '<', ($lastDayOfTheWeek))
        //     ->select('createdAt', 'id', 'finalPrice')
        //     ->get();

        switch ($groupBy) {
            case 'week':
                $groupByMethod = 'groupByWeek';

                break;
            case 'month':
                $groupByMethod = 'groupByMonth';

                break;
            case 'year':
                $groupByMethod = 'groupByYear';

                break;
            case 'date':
            default:
                $groupByMethod = 'groupByDate';
        }

        $days = $this->ordersRepository->aggregate()
            ->where('status', OrdersRepository::COMPLETED_STATUS)
            ->where('createdAt', '>=', $from)
            ->where('createdAt', '<', $to)
            ->{$groupByMethod}('createdAt')
            ->sum(['finalPrice' => 'sales'])
            ->orderBy('_id', 'asc');

        $days = $days->get();

        $sales = collect($days)->map(function ($day, $totalOrder) {
            $day['date'] = $day['_id'];

            unset($day['_id']);

            return $day;
        });


        return [
            'sales' => $sales,
            'from' => $from->format('d-m-Y'),
            'to' => $to->format('d-m-Y'),
        ];
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
     * Get total Sales for the given options
     *
     * @return float
     */
    public function totalSales(array $options = [])
    {
        // $query = $this->ordersRepository->getQuery();
        $query = $this->ordersRepository->aggregate();

        if (!empty($options['from'])) {
            $query->where('createdAt', '>=', static::mongoTime(Carbon::parse($options['from'])));
        }

        if (!empty($options['to'])) {
            $query->where('createdAt', '<', static::mongoTime(Carbon::parse($options['to'])));
        }

        $query->where('status', OrdersRepository::COMPLETED_STATUS);

        $sum = 'finalPrice';

        if (!empty($options['priceOnly'])) {
            $sum = 'subTotal';
        }

        // return $query->get();
        // return $query->sum($sum);
        return Arr::get($query->sum($sum)->get(), '0.finalPrice');
    }

    /**
     * Method totalSalesRestaurants
     *
     * @param array $options
     *
     * @return void
     */
    public function totalSalesRestaurants(array $options = [])
    {
        // $query = $this->ordersRepository->getQuery();
        $query = $this->ordersRepository->aggregate();

        if (!empty($options['from'])) {
            $query->where('createdAt', '>=', static::mongoTime(Carbon::parse($options['from'])));
        }

        if (!empty($options['to'])) {
            $query->where('createdAt', '<', static::mongoTime(Carbon::parse($options['to'])));
        }

        $query->where('status', OrdersRepository::COMPLETED_STATUS);

        $sum = 'finalPrice';

        if (!empty($options['priceOnly'])) {
            $sum = 'subTotal';
        }

        $query->where('productsType', 'food');

        // return $query->get();
        // return $query->sum($sum);
        return Arr::get($query->sum($sum)->get(), '0.finalPrice');
    }

    /**
     * Method totalSalesStore
     *
     * @param array $options
     *
     * @return void
     */
    public function totalSalesStore(array $options = [])
    {
        // $query = $this->ordersRepository->getQuery();
        $query = $this->ordersRepository->aggregate();

        if (!empty($options['from'])) {
            $query->where('createdAt', '>=', static::mongoTime(Carbon::parse($options['from'])));
        }

        if (!empty($options['to'])) {
            $query->where('createdAt', '<', static::mongoTime(Carbon::parse($options['to'])));
        }

        $query->where('status', OrdersRepository::COMPLETED_STATUS);

        $sum = 'finalPrice';

        if (!empty($options['priceOnly'])) {
            $sum = 'subTotal';
        }

        $query->where('productsType', 'products');

        // return $query->get();
        // return $query->sum($sum);
        return Arr::get($query->sum($sum)->get(), '0.finalPrice');
    }

    /**
     * Method totalSalesClubs
     *
     * @param array $options
     *
     * @return void
     */
    public function totalSalesClubs(array $options = [])
    {
        // $query = $this->ordersRepository->getQuery();
        $query = $this->ordersRepository->aggregate();

        if (!empty($options['from'])) {
            $query->where('createdAt', '>=', static::mongoTime(Carbon::parse($options['from'])));
        }

        if (!empty($options['to'])) {
            $query->where('createdAt', '<', static::mongoTime(Carbon::parse($options['to'])));
        }

        $query->where('status', OrdersRepository::COMPLETED_STATUS);

        $sum = 'finalPrice';

        if (!empty($options['priceOnly'])) {
            $sum = 'subTotal';
        }

        $query->where('productsType', 'clubs');

        // return $query->get();
        // return $query->sum($sum);
        return Arr::get($query->sum($sum)->get(), '0.finalPrice');
    }

    /**
     * generate report about order & shipping methods
     *
     * @param array $options
     * @return mixed
     */
    public function getShippingMethodsReport(array $options = [])
    {
        $query = $this->ordersRepository->getQuery();

        $shippingMethods = $this->shippingMethodsRepository->list(['published', true]);

        if (!empty($options['from'])) {
            $query->where('createdAt', '>=', static::mongoTime(Carbon::parse($options['from'])));
        }

        if (!empty($options['to'])) {
            $query->where('createdAt', '<', static::mongoTime(Carbon::parse($options['to'])));
        }

        if (!empty($options['status'])) {
            $query->where('status', $options['status']);
        }

        $sum = 'finalPrice';

        if (!empty($options['priceOnly'])) {
            $sum = 'subTotal';
        }

        $orders = $query->select(["{$sum}", 'shippingMethod'])->get()->whereNotNull('shippingMethod.id');

        return $shippingMethods->map(function ($shippingMethod) use ($sum, $orders) {
            $shippingMethod['totalPrice'] = $orders->where('shippingMethod.id', $shippingMethod->id)->sum($sum);
            $shippingMethod['countOrders'] = $orders->where('shippingMethod.id', $shippingMethod->id)->count();
            unset($shippingMethod['published']);
            unset($shippingMethod['cities']);

            return $shippingMethod;
        });
    }

    /**
     * generate report about order & shipping methods
     *
     * @param array $options
     * @return mixed
     */
    public function getCouponsReport(array $options = [])
    {
        $query = $this->ordersRepository->getQuery();

        $coupons = $this->couponsRepository->list(['published', true, 'paginate' => false]);

        if (!empty($options['from'])) {
            $query->where('createdAt', '>=', static::mongoTime(Carbon::parse($options['from'])));
        }

        if (!empty($options['to'])) {
            $query->where('createdAt', '<', static::mongoTime(Carbon::parse($options['to'])));
        }

        if (!empty($options['status'])) {
            $query->where('status', $options['status']);
        }

        $orders = $query->select(["couponDiscount", 'coupon'])->get()->whereNotNull('coupon.id');

        return $coupons->map(function ($coupon) use ($orders) {
            $coupon['totalDiscount'] = $orders->where('coupon.id', $coupon->id)->sum('couponDiscount');
            $coupon['countOrders'] = $orders->where('coupon.id', $coupon->id)->count();
            unset($coupon['published']);

            return $coupon;
        });
    }

    /**
     * get latest orders
     *
     * @param array $options
     * @return mixed
     */
    public function latestOrders(array $options = [])
    {
        $query = $this->ordersRepository->getQuery();

        //        if (!empty($options['from'])) {
        //            $query->where('createdAt', '>=', static::mongoTime(Carbon::parse($options['from'])));
        //        }
        //
        //        if (!empty($options['to'])) {
        //            $query->where('createdAt', '<', static::mongoTime(Carbon::parse($options['to'])));
        //        }

        if (!empty($options['status'])) {
            $query->where('status', $options['status']);
        }

        $query->latest()->limit(5);

        return $this->ordersRepository->wrapMany($query->get());
    }

    /**
     * Method latestOrderRestaurants
     *
     * @param array $options
     *
     * @return void
     */
    public function latestOrderRestaurants(array $options = [])
    {
        $query = $this->ordersRepository->getQuery();
        if (!empty($options['status'])) {
            $query->where('status', $options['status']);
        }
        $query->where('productsType', 'food');
        $query->latest()->limit(5);

        return $this->ordersRepository->wrapMany($query->get());
    }

    /**
     * Method latestOrderStores
     *
     * @param array $options
     *
     * @return void
     */
    public function latestOrderStores(array $options = [])
    {
        $query = $this->ordersRepository->getQuery();
        if (!empty($options['status'])) {
            $query->where('status', $options['status']);
        }
        $query->where('productsType', 'products');
        $query->latest()->limit(5);

        return $this->ordersRepository->wrapMany($query->get());
    }

    /**
     * Method latestOrderClubs
     *
     * @param array $options
     *
     * @return void
     */
    public function latestOrderClubs(array $options = [])
    {
        $query = $this->ordersRepository->getQuery();
        if (!empty($options['status'])) {
            $query->where('status', $options['status']);
        }
        $query->where('productsType', 'clubs');
        $query->latest()->limit(5);

        return $this->ordersRepository->wrapMany($query->get());
    }

    public function customerReport(array $options = [])
    {
        $customers = $this->customersRepository->listPublished([
            'itemsPerPage' => 15,
            'select' => [
                'id', "firstName", "lastName", "email", "phoneNumber", "totalOrders",
            ],
        ]);


        $query = $this->ordersRepository->getQuery();

        if (!empty($options['from'])) {
            $query->where('createdAt', '>=', static::mongoTime(Carbon::parse($options['from'])));
        }

        if (!empty($options['to'])) {
            $query->where('createdAt', '<', static::mongoTime(Carbon::parse($options['to'])));
        }

        if (!empty($options['status'])) {
            $query->where('status', $options['status']);
        }

        $orders = $query->get();

        $customers = $this->customersRepository->wrapMany($customers);

        foreach ($customers as $key => $customer) {
            $customers[$key]['totalItems'] = $orders->where('customer.id', $customer->id)->sum('totalQuantity');
        }

        return [
            $customers,
            $this->customersRepository->getPaginateInfo(),
        ];
    }

    public function customersReport(array $options = [])
    {
        $customers = $this->customersRepository->listPublished([
            'itemsPerPage' => 15,
            'paginate' => false,
            'select' => [
                'id', "firstName", "lastName", "email", "phoneNumber", "totalOrders",
            ],
        ]);

        $query = $this->ordersRepository->getQuery();

        if (!empty($options['from'])) {
            $query->where('createdAt', '>=', static::mongoTime(Carbon::parse($options['from'])));
        }

        if (!empty($options['to'])) {
            $query->where('createdAt', '<', static::mongoTime(Carbon::parse($options['to'])));
        }

        if (!empty($options['status'])) {
            $query->where('status', $options['status']);
        }

        $orders = $query->get();

        $customers = $this->customersRepository->wrapMany($customers);

        foreach ($customers as $key => $customer) {
            $customers[$key]['totalItems'] = $orders->where('customer.id', $customer->id)->sum('totalQuantity');
        }

        return [
            $customers,
            $this->customersRepository->getPaginateInfo(),
        ];
    }

    /**
     * Method financialReportStore
     *
     * @param $options $options
     *
     * @return void
     */
    public function financialReportStore($options)
    {
        $stores = $this->storesRepository->getQuery()->paginate(15);
        $listStore = [];
        foreach ($stores as $key => $store) {
            $store = $this->storesRepository->wrap($store);
            $listStore[] = $store;
        }

        return $listStore;
    }

    /**
     * Method getPaginateInfoReturnedOrders
     *
     * @return void
     */
    public function getPaginateInfoStore($options)
    {
        $data = $this->storesRepository->getQuery()->paginate(15);

        return $this->paginationInfo = [
            'currentResults' => $data->count(),
            'totalRecords' => $data->total(),
            'numberOfPages' => $data->lastPage(),
            'itemsPerPage' => $data->perPage(),
            'currentPage' => $data->currentPage(),
        ];
    }

    /**
     * Method financialReportResturant
     *
     * @param $options $options
     *
     * @return void
     */
    public function financialReportResturant($options)
    {
        $restaurants = $this->restaurantsRepository->getQuery()->paginate(15);
        $listRestaurant = [];
        foreach ($restaurants as $key => $restaurant) {
            $restaurant = $this->restaurantsRepository->wrap($restaurant);
            $listRestaurant[] = $restaurant;
        }

        return $listRestaurant;
    }

    /**
     * Method getPaginateInfoReturnedOrders
     *
     * @return void
     */
    public function getPaginateInfoResturant($options)
    {
        $data = $this->restaurantsRepository->getQuery()->paginate(15);

        return $this->paginationInfo = [
            'currentResults' => $data->count(),
            'totalRecords' => $data->total(),
            'numberOfPages' => $data->lastPage(),
            'itemsPerPage' => $data->perPage(),
            'currentPage' => $data->currentPage(),
        ];
    }

    /**
     * Method financialReportClub
     *
     * @param $options $options
     *
     * @return void
     */
    public function financialReportClub($options)
    {
        $clubs = $this->clubsRepository->getQuery()->paginate(15);
        $listClub = [];
        foreach ($clubs as  $club) {
            $club = $this->clubsRepository->wrap($club);
            $listClub[] = $club;
        }

        return $listClub;
    }

    /**
     * Method getPaginateInfoReturnedOrders
     *
     * @return void
     */
    public function getPaginateInfoClub($options)
    {
        $data = $this->clubsRepository->getQuery()->paginate(15);

        return $this->paginationInfo = [
            'currentResults' => $data->count(),
            'totalRecords' => $data->total(),
            'numberOfPages' => $data->lastPage(),
            'itemsPerPage' => $data->perPage(),
            'currentPage' => $data->currentPage(),
        ];
    }

    /**
     * Method financialReportClinic
     *
     * @param $options $options
     *
     * @return void
     */
    public function financialReportClinic($options)
    {
        $nutritionSpecialists = $this->nutritionSpecialistsRepository->getQuery()->paginate(15);
        $listNutritionSpecialists = [];
        foreach ($nutritionSpecialists as  $nutritionSpecialist) {
            $nutritionSpecialist = $this->nutritionSpecialistsRepository->wrap($nutritionSpecialist);
            $listNutritionSpecialists[] = $nutritionSpecialist;
        }

        return $listNutritionSpecialists;
    }

    /**
     * Method getPaginateInfoReturnedOrders
     *
     * @return void
     */
    public function getPaginateInfoClinic($options)
    {
        $data = $this->nutritionSpecialistsRepository->getQuery()->paginate(15);

        return $this->paginationInfo = [
            'currentResults' => $data->count(),
            'totalRecords' => $data->total(),
            'numberOfPages' => $data->lastPage(),
            'itemsPerPage' => $data->perPage(),
            'currentPage' => $data->currentPage(),
        ];
    }
}
