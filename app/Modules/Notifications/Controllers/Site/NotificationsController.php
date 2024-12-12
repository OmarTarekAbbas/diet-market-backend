<?php

namespace App\Modules\Notifications\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class NotificationsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'notifications';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'user' => user()->id,
            'userType' => user()->accountType(),
        ];

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'records' => $this->repository->list($options),
            'paginationInfo' => $this->repository->getPaginateInfo(),
            'markAllAsSeen' => $this->repository->markAllAsSeen(user()),
        ]);
    }

    /**
     * Remove the given notification id
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        if (!$this->repository->notificationBelongTo($id, user())) {
            return $this->badRequest('notFound');
        }

        $this->repository->delete($id);

        return $this->success();
    }

    /**
     * Remove all notifications for current user
     *
     * @return Response
     */
    public function destroyAll()
    {
        $this->repository->deleteAllFor(user());

        return $this->success();
    }

    /**
     * Mark the given notification as seen
     *
     * @param int $id
     * @return Response
     */
    public function markAsSeen($id)
    {
        if (!$this->repository->notificationBelongTo($id, user())) {
            return $this->badRequest('notFound');
        }

        $this->repository->markAsSeen($id);

        return $this->success();
    }

    /**
     * Mark all notifications for current user as seen
     *
     * @return Response
     */
    public function markAllAsSeen()
    {
        $this->repository->markAllAsSeen(user());

        return $this->success();
    }
}
