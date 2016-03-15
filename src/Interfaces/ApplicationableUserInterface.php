<?php

namespace Nebo15\LumenApplicationable\Interfaces;

interface ApplicationableUserInterface
{
    public function getApplications();
    public function setCurrentApplication($application);
    public function getId();
}