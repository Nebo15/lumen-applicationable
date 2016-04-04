<?php

namespace Nebo15\LumenApplicationable\Contracts;

/**
 * Interface ApplicationableUser
 * Interface for User Model
 * @package Nebo15\LumenApplicationable\Contracts
 */
interface ApplicationableUser
{
    /**
     * Get list of applications which available for user
     * @return mixed
     */
    public function getApplications();

    /**
     * Set current application in which user authorized
     * @param $application
     * @return mixed
     */
    public function setCurrentApplication($application);

    /**
     * Get user id
     * @return mixed
     */
    public function getId();

    /**
     * Get user from application (rules, access info, tokens)
     * @return mixed
     */
    public function getApplicationUser();

    /**
     * Get current application in which user authorized
     * @return mixed
     */
    public function getCurrentApplication();

    /**
     * Get user from application and set it into model
     * @return mixed
     */
    public function getAndSetApplicationUser();
}
