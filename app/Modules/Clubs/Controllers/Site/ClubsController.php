<?php

namespace App\Modules\Clubs\Controllers\Site;

use Illuminate\Http\Request;
use App\Modules\General\Helpers\Visitor;
use HZ\Illuminate\Mongez\Managers\ApiController;

class ClubsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'clubs';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $customer = user();
        if (!$customer) {
            $customer = $this->guestsRepository->getByModel('customerDeviceId', Visitor::getDeviceId());
        }

        if (user()) {
            $healthyData = $this->healthyDatasRepository->getByModel('customerId', $customer->id);
        } else {
            $healthyData = $this->healthyDatasRepository->getByModel('customerDeviceId', Visitor::getDeviceId());
        }

        $options = [
            'location' => $customer->location,
            'gender' => $healthyData['healthInfo']['gender'],
        ];

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        // $options['select'] = ['name', 'address', 'logo'];

        return $this->success([
            'records' => $this->repository->listPublished($options),
            'subscribeClubs' => $this->repository->subscribeClubCustomer(),
            'paginationInfo' => $this->repository->getPaginateInfo(),
            'returnOrderStatus' => $this->settingsRepository->getSetting('ReturnedOrder', 'returnSystemStatus'),
        ]);
    }

    /**
     * Method schedule
     *
     * @param $id $id
     * @param Request $request
     *
     * @return void
     */
    public function schedule($id, Request $request)
    {
        $club = $this->repository->get($id);

        if (!$club) {
            return $this->badRequest(trans('club.notfound'));
        }

        $branches = $this->repository->schedule($club);

        return $this->success([
            'club' => $club,
            'branches' => $branches,
        ]);
    }

    /**
     * {@inheritDoc}
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
        $optionsReviews = [
            'club' => (int) $id,
            'sortRating' => $request->sortRating,
            'highestRating' => $request->highestRating,
            'lowestRating' => $request->lowestRating,
            'latest' => $request->latest,
            'oldest' => $request->oldest,
            'sort' => $request->sort,
            'published' => true,
        ];

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'record' => $this->repository->showClub($id),
            'reviews' => $this->clubReviewsRepository->list($optionsReviews),
            'countReviews' => $this->clubReviewsRepository->list($optionsReviews)->count(),
            'paginationInfo' => $this->clubReviewsRepository->getPaginateInfo(),
            'subscribeClubs' => $this->repository->subscribeClubs($id) ?? false,
            'clubBookings' => $this->clubBookingsRepository->clubBookings($id) ?? false,
            'listClubBookings' => $this->clubBookingsRepository->list($options) ?? false,
        ]);
    }

    /**
     * get Seller's getMyClub Info
     */
    public function getMyClub(Request $request)
    {
        $user = user();
        $clubId = $user->club['id'];

        return $this->success([
            'record' => $this->repository->showClub($clubId),
            // 'branches' =>  $this->branchesClubsRepository->getBranchesClubs($clubId),
            // 'packages' =>  $this->packagesClubsRepository->getPackagesClubs($clubId),
        ]);
    }

    /**
     * Update updateMyClub's updateMyClub Info
     */
    public function updateMyClub(Request $request)
    {
        $user = user();

        $clubId = $user->club['id'];
        $this->repository->update($clubId, $request->all());

        return $this->success([
            'record' => $this->repository->showClub($clubId),
        ]);
    }

    /**
     * Method getHealthyDataUser
     *
     * @param $id $id
     *
     * @return void
     */
    public function getHealthyDataUser($id)
    {
        $optionStore = [
            'customer' => $id,
            'type' => 'products',
        ];
        $optionFood = [
            'customer' => $id,
            'type' => 'food',
        ];

        return $this->success([
            'customer' => $this->customersRepository->get((int) $id),
            'healthydata' => $this->healthyDatasRepository->getByModel('customerId', (int) $id),
            'orderStore' => $this->ordersRepository->list($optionStore),
            'orderFood' => $this->ordersRepository->list($optionFood),
            // 'nutritionSpecialistsNotesCustomer' => $this->nutritionSpecialistsCustomerNotesRepository->nutritionSpecialistsNotesCustomer($id),
        ]);
    }

    public function listCustomer(Request $request)
    {
        $options = [
            'club' => $request->club,
        ];

        return $this->success([
            'records' => $this->customersRepository->list($options),
            'paginationInfo' => $this->customersRepository->getPaginateInfo(),
        ]);
    }
}
