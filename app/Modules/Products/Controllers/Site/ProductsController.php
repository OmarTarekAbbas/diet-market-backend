<?php

namespace App\Modules\Products\Controllers\Site;

use Illuminate\Http\Request;
// use HZ\Illuminate\Mongez\Managers\ApiController;
use App\Modules\General\Helpers\Visitor;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ProductsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'products',
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
                'discount.value' => 'required_if:discount.type,percentage,amount',
                'discount.startDate' => 'required_if:discount.type,percentage,amount|date',
                'discount.endDate' => 'required_if:discount.type,percentage,amount|date',
                'priceInSubscription' => 'required_if:inSubscription,true',
                'minQuantity' => 'required|numeric|min:1',
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
        $this->middleware('isStoreManager', ['except' => ['index', 'show']]);
    }

    public function index(Request $request)
    {
        $options = [
            'category' => (int) $request->category,
            'brand' => (int) $request->brand,
            'store' => (int) $request->storeManager,
            'storeManager' => (int) $request->storeManager,
            'type' => 'products',
            'alphabetic' => true,
            'storePublished' => true,
        ];

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        $listPareantForRequest = $this->ListParent($request);

        return $this->success([
            'records' => $this->repository->listPublished($options),
            $listPareantForRequest[0] => $listPareantForRequest[1],

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

        $product = $this->repository->show($id);
        // $options = [
        //     'category' => $product->category['id'],
        //     'page' => $request->page,
        //     'itemsPerPage' => $request->itemsPerPage,
        // ];
        $optionsReviews = [
            'product' => $id,
            'page' => $request->page,
            'itemsPerPage' => $request->itemsPerPage,
            'published' => true,

        ];

        // if ($product->relatedProducts) {
        //     $relatedProducts = $this->repository->wrapMany($product->relatedProducts);
        // } else {
        //     $relatedProducts = $this->repository->relatedProducts($product->category['id'], $id);
        // }

        $relatedProducts = $this->repository->wrapMany($product->relatedProducts);
        
        if (request()->url() == url('api/storeManagers/products/' . $id)) {
            $record = 'record';
        } else {
            $record = 'records';
        }

        // return $this->success([
        //     $record => $product,
        //     'relatedProducts' => $relatedProducts,
        //     'reviewsProducts' => $this->productReviewsRepository->list($optionsReviews),
        //     'cartCount' => $this->cartRepository->countCart(),
        //     'paginationInfo' => $this->repository->getPaginateInfo(),
        // ]);

        if (user()) {
            return $this->success([
                $record => $product,
                'relatedProducts' => $relatedProducts,
                'reviewsProducts' => $this->productReviewsRepository->list($optionsReviews),
                'cartCount' => $this->cartRepository->countCart(),
                'paginationInfo' => $this->repository->getPaginateInfo(),
            ]);
        } else {
            $deviceId = Visitor::getDeviceId();
            $cartMeal = $this->cartRepository->getQuery()->where('deviceId', $deviceId)->where('type', 'food')->first();

            return $this->success([
                $record => $product,
                'relatedProducts' => $relatedProducts,
                'reviewsProducts' => $this->productReviewsRepository->list($optionsReviews),
                'cartCount' => $this->cartRepository->countCart(),
                'paginationInfo' => $this->repository->getPaginateInfo(),
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
        if ($request->category) {
            $nameRecords = 'category';
            $dataRepo = $this->categoriesRepository->get($request->category);
        } elseif ($request->brand) {
            $nameRecords = 'brand';
            $dataRepo = $this->brandsRepository->get($request->brand);
        } elseif ($request->storeManager) {
            $nameRecords = 'storeManager';
            $dataRepo = $this->storeManagersRepository->get($request->storeManager);
        }

        return [$nameRecords ?? null, $dataRepo ?? null];
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

    /**
     * get Seller's Store Info
     */
    public function getMyStore(Request $request)
    {
        // dd('sdsd');
        $user = user();

        if ($user->accountType() != 'StoreManager') {
            return $this->unauthorized(trans('auth.unauthorized'));
        }
        // dd($request->id);
        $options = [
            'category' => $request->category,
            'brand' => $request->brand,
            'store' => (int) $user->id,
            'id' => $request->id,
            'type' => 'products',
            'itemsPerPage' => $request->itemsPerPage,
            'page' => $request->page,
        ];

        return $this->success([
            'records' => $this->repository->list($options),
            'paginationInfo' => $this->repository->getPaginateInfo(),

        ]);
    }
}
