<?php

namespace App\Modules\Logs\Services;

use Illuminate\Support\Facades\App;
use HZ\Illuminate\Mongez\Events\Events;

class LogsEvents
{
    /**
     * logs repository
     */
    private function repository()
    {
        return repo('logs');
    }

    /**
     * retrieve model name from the given class
     *
     * @return string
     */
    private function getModelName($model)
    {
        $model = get_class($model); // REturn class name with his namespace

        $path = explode('\\', $model);

        return strtolower(array_pop($path));
    }

    /**
     * get model class trans
     *
     * @return string
     */
    private function getTrans($model)
    {
        return trans('log.' . $this->getModelName($model));
    }

    /**
     * log action on create
     *
     * @param Model $model
     * @param Request $request
     * @return void
     */
    public function onCreate($model, $request)
    {
        $modelName = $this->getTrans($model);

        $action = trans('log.createLogged', ['model' => $modelName]);

        $this->log($action, $model);
    }

    /**
     * log action on create
     *
     * @param Model $model
     * @param Request $request
     * @param Model $oldModel
     * @return void
     */
    public function onUpdate($model, $request, $oldModel)
    {
        $modelName = $this->getTrans($model);

        $action = trans('log.updateLogged', ['model' => $modelName, 'id' => $model->id]);

        $this->log($action, $model, $oldModel);
    }

    /**
     * Remove log
     *
     * @param Model $model
     * @param int $id
     * @return void
     */
    public function onDelete($model, $id)
    {
        $modelName = $this->getTrans($model);

        $action = trans('log.deleteLogged', ['model' => $modelName, 'id' => $id]);

        $this->log($action, $model);
    }

    /**
     * add new log
     *
     * @return void
     */
    public function log($action, $model, $oldModel = null)
    {
        // $modelName = $this->getTrans($model);
        $dataLogged = [
            'user' => user() ? user()->sharedInfo() : '',
            'action' => $action,
            // 'job' => $modelName,
            'data' => $model->toArray(),
            'oldData' => $oldModel ? $oldModel->toArray() : [],
        ];

        $this->repository()->create($dataLogged);
    }

    /**
     * Subscribe event name
     *
     * @return void
     */
    public function subscribe()
    {
        $events = App::make(Events::class);

        foreach (array_keys(config('mongez.repositories')) as $repository) {
            //  important line .. to solve infinite loop when add log
            //
            if ($repository == 'logs') {
                continue;
            }

            // Log For Create New Record
            $events->subscribe($repository . '.create', static::class . '@' . 'onCreate');

            // Log For Update  Record
            $events->subscribe($repository . '.update', static::class . '@' . 'onUpdate');

            // Log For Delete Record
            $events->subscribe($repository . '.delete', static::class . '@' . 'onDelete');
        }
    }
}
