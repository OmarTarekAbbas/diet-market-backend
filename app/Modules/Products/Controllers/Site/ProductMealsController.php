<?php

namespace App\Modules\Products\Controllers\Site;

use Illuminate\Http\Request;
// use HZ\Illuminate\Mongez\Managers\ApiController;
use App\Modules\General\Helpers\Visitor;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ProductMealsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'productMeals',
        'listOptions' => [
            'select' => [
                // 'name', 'description', 'image', 'finalPrice'
            ],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                // 'discount.type' => 'in:amount,percentage',
                // 'discount.value' => 'required_if:discount.type,percentage,amount',
                // 'discount.startDate' => 'required_if:discount.type,percentage,amount|date',
                // 'discount.endDate' => 'required_if:discount.type,percentage,amount|date',
                'priceInSubscription' => 'required_if:inSubscription,true',
                // 'minQuantity' => 'required|numeric|min:1',
                // 'unit' => 'required',
                'category' => 'required',
            ],
            'store' => [
                'images' => 'required|array',
                'images.*' => 'image',
            ],
            'update' => [
                'images' => 'nullable|array',
                'images.*' => 'image',
            ],
        ],
    ];

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();

        // $this->middleware('auth', ['except' => ['index', 'show']]);
        // $this->middleware('restaurantManager', ['except' => ['index', 'show']]);
    }

    public function index(Request $request)
    {
        $options = [
            'restaurant' => $request->restaurant,
            'type' => 'food',
        ];
        $listPareantForRequest = $this->ListParent($request);

        return $this->success([
            'records' => $this->repository->list($options),
            $listPareantForRequest[0] => $listPareantForRequest[1],
            // 'paginate' => true,
            // 'itemsPerPage' => 10,
            'paginationInfo' => $this->repository->getPaginateInfo(),
        ]);
    }

    /**
     * Method show
     *
     * @param $id $id
     *show
     * @return Response
     */
    public function show($id, Request $request)
    {
        if (!$this->repository->has($id)) {
            return $this->notFound(trans('response.notFound'));
        }

        $product = $this->repository->get($id);
        $options = [
            // 'category' => $product->category['id'],
            'page' => $request->page,
            'itemsPerPage' => $request->itemsPerPage,
        ];
        // $optionsReviews = [
        //     'product' => $id,
        //     'page' => $request->page,
        //     'itemsPerPage' => $request->itemsPerPage,
        // ];

        if (request()->url() == url('api/restaurantManager/productMeals/' . $id)) {
            $record = 'record';
        } else {
            $record = 'records';
        }

        // return $this->success([
        //     $record => $product,
        //     // 'relatedProducts' => $this->repository->list($options),
        //     // 'reviewsProducts' => $this->productReviewsRepository->list($optionsReviews),
        //     'cartCount' => $this->cartRepository->countCart(),
        //     // 'paginationInfo' => $this->repository->getPaginateInfo(),
        // ]);


        if (user()) {
            return $this->success([
                $record => $product,
                'cartCount' => $this->cartRepository->countCart(),
            ]);
        } else {
            $deviceId = Visitor::getDeviceId();
            $cartMeal = $this->cartRepository->getQuery()->where('deviceId', $deviceId)->where('type', 'food')->first();

            return $this->success([
                $record => $product,
                'cartCount' => $this->cartRepository->countCart(),
                'customer' => ['cartMeal' => $cartMeal ? $this->cartRepository->wrap($cartMeal) : null],
            ]);
        }
    }

    /**
     * Method ListParent
     *
     * @param $request $request
     *
     * @return void
     */
    public function ListParent($request)
    {
        if ($request->restaurant) {
            $nameRecords = 'restaurant';
            $dataRepo = $this->restaurantsRepository->get($request->restaurant);
        }

        return [$nameRecords ?? null, $dataRepo ?? null];
    }

    /**
     * get Seller's Store Info
     */
    public function getMyStore(Request $request)
    {
        $user = user();
        // dd($user);

        // if ($user->accountType() != 'StoreManager') {
        //     return $this->unauthorized(trans('auth.unauthorized'));
        // }
        $options = [
            'category' => $request->category,
            // 'brand' => $request->brand,
            'restaurant' => $user['restaurant']['id'],
            'type' => 'food',
            'paginate' => true,
            'itemsPerPage' => 15,
        ];

        return $this->success([
            'records' => $this->repository->list($options),
            'paginationInfo' => $this->productMealsRepository->getPaginateInfo(),

        ]);
    }

    /**
     * Method store
     *
     * @param Request $request
     *
     * @return void
     */
    public function store(Request $request)
    {
        try {
            $updateSection = $this->repository->create($request);

            return $this->success([
                'record' => $this->repository->wrap($updateSection),
                'success' => true,
            ]);
        } catch (\Exception $exception) {
            return $this->badRequest($exception->getMessage());
        }
    }
}
