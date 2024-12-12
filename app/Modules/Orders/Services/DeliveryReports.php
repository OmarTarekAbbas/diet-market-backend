<?php

namespace App\Modules\Orders\Services;

use DateTime;
use DateTimeZone;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Modules\Coupons\Repositories\CouponsRepository;
use App\Modules\Orders\Repositories\OrderDeliveryRepository;
use App\Modules\ShippingMethods\Repositories\ShippingMethodsRepository;

class DeliveryReports
{
    /**
     * Orders Repository
     *
     * @var OrderDeliveryRepository
     */
    public $orderDeliveryRepository;

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
     * @param OrderDeliveryRepository $OrderDeliveryRepository
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    public function __construct(OrderDeliveryRepository $orderDeliveryRepository)
    {
        $this->orderDeliveryRepository = $orderDeliveryRepository;
        $this->shippingMethodsRepository = repo('shippingMethods');
        $this->couponsRepository = repo('coupons');
        $this->customersRepository = repo('customers');
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
     * Method monthCount
     *
     * @param $year $year
     * @param $month $month
     *
     * @return void
     */
    public function monthCount($year, $month)
    {
        $date = Carbon::parse("01-{$month}-{$year}");

        return $this->counts([
            'from' => $date,
            'to' => $date->copy()->lastOfMonth(),
            // 'groupBy' => 'week',
        ]);

        $weeks = [];

        for ($week = 1; $week <= 4; $week++) {
            $weeks[] = $this->counts([
                'from' => $date->copy(),
                'to' => $date->addDays(7),
            ]);
        }

        if ($date->copy()->endOfMonth()->format('d') > 28) {
            $weeks[] = $this->counts([
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
     * Method yearCount
     *
     * @param $year $year
     *
     * @return void
     */
    public function yearCount($year)
    {
        $date = Carbon::parse("01-01-{$year}");

        return $this->counts([
            'from' => $date,
            'to' => $date->copy()->lastOfYear(),
            'groupBy' => 'month',
        ]);

        $weeks = [];

        for ($week = 1; $week <= 4; $week++) {
            $weeks[] = $this->counts([
                'from' => $date->copy(),
                'to' => $date->addDays(7),
            ]);
        }

        if ($date->copy()->endOfMonth()->format('d') > 28) {
            $weeks[] = $this->counts([
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

        $days = $this->orderDeliveryRepository->aggregate()
            ->where('status', OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS)
            ->where('createdAt', '>=', $from)
            ->where('createdAt', '<', $to)
            ->{$groupByMethod}('createdAt')
            ->sum(['deliveryCommission' => 'sales'])
            ->orderBy('_id', 'asc');

        $days = $days->get();

        $sales = collect($days)->map(function ($day) {
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
     * Method counts
     *
     * @param array $options
     *
     * @return void
     */
    public function counts(array $options)
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

        $days = $this->orderDeliveryRepository->aggregate()
            ->where('status', OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS)
            ->where('createdAt', '>=', $from)
            ->where('createdAt', '<', $to);
        $days = $days->get();

        $sales = collect($days)->map(function ($day) {
            $day['date'] = $day['_id'];

            unset($day['_id']);

            return $day;
        });

        return [
            'count' => count($sales),
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

        $query->where('status', OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS);

        $sum = 'finalPrice';

        if (!empty($options['priceOnly'])) {
            $sum = 'subTotal';
        }

        // return $query->get();
        // return $query->sum($sum);
        return Arr::get($query->sum($sum)->get(), '0.finalPrice');
    }
}
