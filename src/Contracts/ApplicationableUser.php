<?php

namespace Nebo15\LumenApplicationable\Contracts;

interface ApplicationableUser
{
    public function getApplications();

    public function setCurrentApplication($application);

    public function getId();

    public function getApplicationUser();

    public function getCurrentApplication();

    public function getAndSetApplicationUser();
}
