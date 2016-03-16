<?php
namespace Nebo15\LumenApplicationable\Contracts;

interface Applicationable
{

    public function getApplications();

    public function isApplicationHasAccess($application);

    public function addApplication($application);

    public function removeApplication($application);
}
