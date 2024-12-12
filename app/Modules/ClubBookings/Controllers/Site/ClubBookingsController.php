<?php

namespace App\Modules\ClubBookings\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;
use App\Modules\ClubBookings\Traits\ChangeStatus;

class ClubBookingsController extends ApiController
{
    use ChangeStatus;

    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'clubBookings';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'customer' => user()->id,
        ];

        if ($request->club) {
            $options['club'] = $request->club;
        }
        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'records' => $this->repository->list($options),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function store(Request $request)
    {
        $validator = $this->scan($request);

        if ($validator->fails()) {
            return $this->badRequest($validator->errors());
        }
        // dd($request->clubBranch);
        $checkClubAvaliable = $this->branchesClubsRepository->getBranchClub((int) $request->clubBranch);
        // dd($checkClubAvaliable);
        if ($checkClubAvaliable) {
            $this->repository->createBooking($request);
        } else {
            return $this->badRequest(trans('club.booking.doNOtAllowBooking'));
        }

        return $this->success();
    }

    /**
     * Validate Contact Message
     */
    public function scan(Request $request)
    {
        return Validator::make($request->all(), [
            'clubBranch' => 'required|integer',
            // 'bookingDates' => 'required'
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

    //////////////////////////////////////////////////////////////////////////////////
    /**
     * {@inheritDoc}
     */
    public function indexClubManagers(Request $request)
    {
        $user = user();
        // dd($user->club['id']);
        // $options['club'] = $user->club['id'];
        // $options['id'] = $request['id'];
        // $options['customer'] = (int)$request['customer'];
        // $options['status'] = $request['status'];

        $options = [
            'club' => $user->club['id'],
            'id' => $request['id'],
            'customer' => $request['customer'],
            'status' => $request['status'],
        ];

        return $this->success([
            'records' => $this->repository->list($options),
            'paginationInfo' => $this->repository->getPaginateInfo(),

        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function storeClubManagers(Request $request)
    {
        $validator = $this->scan($request);

        if ($validator->fails()) {
            return $this->badRequest($validator->errors());
        }
        $this->repository->create($request);

        return $this->success();
    }
}
