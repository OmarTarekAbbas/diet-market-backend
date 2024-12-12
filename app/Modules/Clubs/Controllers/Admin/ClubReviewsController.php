<?php

namespace App\Modules\Clubs\Controllers\Admin;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ClubReviewsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'clubReviews',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [],
            'store' => [],
            'update' => [],
        ],
    ];

    /**
     * Method index
     *
     * @param Request $request
     *
     * @return void
     */
    public function index(Request $request)
    {
        $options = [
            'clubId' => (int) $request->clubId,
            'sortRating' => $request->sortRating,
            'highestRating' => $request->highestRating,
            'lowestRating' => $request->lowestRating,
            'latest' => $request->latest,
            'oldest' => $request->oldest,
            'sort' => $request->sort,
            'id' => (int) $request->id,
            'customer' => (int) $request->customer,
        ];

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'records' => $this->repository->clubsReview($options),
            'paginationInfo' => $this->repository->getPaginateInfo(),
        ]);
    }

    /**
     * Method update
     *
     * @param Request $request
     * @param $id $id
     *
     * @return void
     */
    public function update(Request $request, $id)
    {
        if ($this->repository->update($id, $request)) {
            return $this->success([
                'record' => $this->repository->clubsReviewId($id),
            ]);
        }
    }
}
