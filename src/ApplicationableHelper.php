<?php
namespace Nebo15\LumenApplicationable;

use Nebo15\LumenApplicationable\Contracts\Applicationable as ApplicationableContract;
use Nebo15\LumenApplicationable\Models\Application;

class ApplicationableHelper
{
    public static function addApplication(ApplicationableContract $model)
    {

        $model->addApplication(app()->make('Nebo15\LumenApplicationable\Models\Application')->_id);
    }

    public static function getApplicationId()
    {
        return app()->make('Nebo15\LumenApplicationable\Models\Application')->_id;
    }
}
