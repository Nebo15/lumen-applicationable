<?php
namespace Nebo15\LumenApplicationable\Contracts;

/**
 * Interface for Applicationable Models
 *
 * Interface Applicationable
 * @package Nebo15\LumenApplicationable\Contracts
 */
interface Applicationable
{
    /**
     * Get all of applications which has access to the model
     * @return array
     */
    public function getApplications();

    /**
     * Check if current applications can view this model
     * @param $application
     * @return boolean
     */
    public function isApplicationHasAccess($application);

    /**
     * Add another application for the model
     * @param $application
     * @return self
     */
    public function addApplication($application);

    /**
     * Remove application from the model
     * @param $application
     * @return self
     */
    public function removeApplication($application);

    /**
     * Get model by Id
     * @param $id
     * @return self | ModelNotFoundException
     */
    public static function findById($id);
}
