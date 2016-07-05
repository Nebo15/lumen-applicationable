<?php
namespace Nebo15\LumenApplicationable\Exceptions;

use Exception;

class AccessDeniedException extends Exception
{
    public $scopes = [];

    public function __construct($message = "", $code = 0, Exception $previous = null, $scopes = [])
    {
        $this->scopes = $scopes;
        return parent::__construct($message, $code, $previous);
    }
}
