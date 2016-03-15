<?php

namespace Nebo15\LumenApplicationable\Traits;

use Nebo15\LumenApplicationable\Models\Application;

trait ApplicationableUserTrait
{
    private $application;
    private $applicationUser;

    /**
     * @return Application
     */
    public function getApplicationUser()
    {
        return $this->applicationUser;
    }

    public function getApplications()
    {
        #TODO: go to projects table and get all projects by user
    }

    public function setCurrentApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    public function getCurrentApplication()
    {
        return $this->application;
    }

    public function getAndSetApplicationUser()
    {
        $this->applicationUser = $this->application->getUser($this->getId());

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }
}
