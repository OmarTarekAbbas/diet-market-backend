<?php

namespace App\Modules\Clubs\Controllers\Admin;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ClubsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'clubs',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                // 'branches.*.workTimes' => 'required|min:1',
                // 'branches.*.workTimes.*' => 'array|required|min:1',
                // 'branches.*.workTimes.*.open' => 'min:1',
                // 'branches.*.workTimes.*.available' => 'min:1|in:yes',
                'logo' => 'max:' . kbit,
                'images' => 'max:' . kbit,
                'cover' => 'max:' . kbit,
                'commercialRegisterImage' => 'max:' . kbit,

            ],
            'store' => [
                'name.*.text' => 'required|min:2',
                'aboutClub' => 'required',
                'gender' => 'required',
                // 'address' => 'required',
                // 'packages' => 'required'
            ],
            'update' => [],
        ],
    ];

    public function index(Request $request)
    {
        return $this->success([
            'records' => $this->repository->listClubs($request),
            'paginationInfo' => $this->repository->getPaginateInfoClubs($request),
        ]);
    }

    /**
     * Method show
     *
     * @param $id $id
     * @param Request $request
     *
     * @return void
     */
    public function show($id, Request $request)
    {
        $club = $this->repository->get($id);
        if (!$club) {
            return $this->badRequest(trans('club.notfound'));
        }

        $options = [
            'club' => (int) $id,
        ];

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }
        // dd('sdsd');

        return $this->success([
            'record' => $this->repository->showClub($id),
            // 'branches' =>  $this->branchesClubsRepository->getBranchesClubs($id),
            // 'packages' =>  $this->packagesClubsRepository->getPackagesClubs($id),
        ]);
    }

    /**
     * Method destroy
     *
     * @param $id $id
     * @param Request $request
     *delete categories
     * @return
     */
    public function destroy($id)
    {
        $destroy = $this->repository->deleteClubsOrder((int) $id);
        if ($destroy) {
            return $this->badRequest(' لا يمكن حذف نادي من لوحة التحكم  في حالة وجود اشتراك مفعل');
        } else {
            if ($this->repository->get($id)) {
                $this->repository->delete($id);

                return $this->success();
            }

            return $this->badRequest(trans('errors.notFound'));
        }
    }
}
