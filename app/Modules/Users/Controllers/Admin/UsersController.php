<?php

namespace App\Modules\Users\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class UsersController extends AdminApiController
{
    /**
     * A flag that determine if there is a validation on the user group
     *
     * @const bool
     */
    protected const HAS_GROUP = true;

    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'users',
        'listOptions' => [
            'select' => ['id', 'name', 'group', 'email', 'type'],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                // 'name' => 'required|string',
            ],
            'store' => [],
            'update' => [],
        ],
    ];

    /**
     * Make custom validation for store.
     *
     * @param mixed $request
     *
     * @return array
     */
    protected function storeValidation($request): array
    {
        $rules = [
            'email' => [
                'required',
                'email',
                Rule::unique($this->repository->getTableName(), 'email'),
            ],
            'password' => 'required|min:8',
        ];

        if (static::HAS_GROUP === true) {
            $rules['group'] = [
                'required',
                'int',
                // Rule::exists($this->usersGroupsRepository->getTableName(), 'id'),
            ];
        }

        return $rules;
    }

    /**
     * Make custom validation for store.
     *
     * @param int $id
     * @param mixed $request
     * @return array
     */
    protected function updateValidation($id, $request): array
    {
        return [
            'email' => [
                'required',
                Rule::unique($this->repository->getTableName(), 'email')->where(function ($query) use ($id) {
                    $query->whereNull('deleted_at');
                    $query->where('id', '!=', (int) $id);
                }),
            ],
        ];
    }

    /**
     * get logged in user
     * @return Response|string
     */
    public function me()
    {
        return $this->success([
            'user' => $this->repository->wrap(user()),
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
        if ($this->repository->get($id)) {
            $extensionEmail = pathinfo($request->email, PATHINFO_EXTENSION);
            if ($extensionEmail == 'net' || $extensionEmail == 'com' || $extensionEmail == 'org' || $extensionEmail == 'market' || $extensionEmail == 'sa' || $extensionEmail == 'eg') {
                $updateSection = $this->repository->update($id, $request);

                return $this->success([
                    'record' => $this->repository->wrap($updateSection),
                ]);
            } else {
                return $this->badRequest('برجاء التاكد من صيغة الاميل');
            }
        }

        return $this->badRequest(trans('errors.notFound'));
    }

    /**
     * Method update
     *
     * @param Request $request
     * @param $id $id
     *
     * @return void
     */
    public function store(Request $request)
    {
        $validator = $this->scan($request);

        if ($validator->fails()) {
            return $this->badRequest($validator->errors());
        }
        $extensionEmail = pathinfo($request->email, PATHINFO_EXTENSION);
        if ($extensionEmail == 'net' || $extensionEmail == 'com' || $extensionEmail == 'org' || $extensionEmail == 'market' || $extensionEmail == 'sa' || $extensionEmail == 'eg') {
            $updateSection = $this->repository->create($request);

            return $this->success([
                'record' => $this->repository->wrap($updateSection),
            ]);
        } else {
            return $this->badRequest('برجاء التاكد من صيغة الاميل');
        }

        return $this->badRequest(trans('errors.notFound'));
    }

    /**
     * Determine whether the passed values are valid
     *
     * @param Request $request
     * @return mixed
     */
    protected function scan(Request $request)
    {
        $table = $this->repository->getTableName();

        return Validator::make($request->all(), [
            'password' => 'required|min:6|max:255',
            'email' => 'required|unique:' . $table,
        ]);
    }
}
