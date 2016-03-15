<?php

namespace Nebo15\LumenApplicationable\Traits;

use Nebo15\LumenApplicationable\Models\Application;

trait ApplicationableUserTrait
{
    private $application;
    private $applicationUser;

    public function __call($name, $arguments)
    {
        return parent::__call($name, $arguments);
    }

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