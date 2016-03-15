<?php
namespace Nebo15\LumenApplicationable\Contracts;

interface ApplicationableContract
{

    public function getApplications();

    public function isApplicationHasAccess($application);

    public function addApplication($application);

    public function removeApplication($application);
}
