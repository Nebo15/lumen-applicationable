<?php
namespace Nebo15\LumenApplicationable\Traits;

use Nebo15\LumenApplicationable\Exceptions\IdNotFoundException;

trait ApplicationableTrait
{
    public function __construct()
    {
        $this->attributes = array_merge($this->attributes, ['applications' => []]);
        $this->visible[] = 'applications';

        return parent::__construct();
    }

    public function getApplications()
    {
        return $this->applications;
    }

    public function isApplicationHasAccess($application)
    {
        return in_array($application, $this->getApplications());
    }

    public function addApplication($application)
    {
        if (!in_array($application, $this->getApplications())) {
            $this->setAttribute('applications', array_merge($this->getApplications(), [$application]));
        }
    }

    public function removeApplication($application)
    {
        if (in_array($application, $this->getApplications())) {
            $applications = $this->getApplications();
            foreach ($applications as $key => $application_val) {
                if ($application_val = $application) {
                    unset($applications[$key]);
                }
            }
            $applications = array_values($applications);
            $this->setAttribute('applications', $applications);
        }
    }

    public static function findById($id)
    {
        if (empty($id)) {
            throw new IdNotFoundException;
        }

        $query = self::where(self::PRIMARY_KEY, $id);
        $query->where(['applications' => ['$in' => [ApplicationableHelper::getApplicationId()]]]);
        return $query->firstOrFail();
    }
}
