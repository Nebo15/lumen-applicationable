<?php
namespace Nebo15\LumenApplicationable;

use Nebo15\LumenApplicationable\Contracts\Applicationable as ApplicationableContract;

class ApplicationableHelper
{
    public static function addApplication(ApplicationableContract $model)
    {
        $application = app()->offsetGet('applicationable.application');
        $model->addApplication($application->_id);
    }
}
