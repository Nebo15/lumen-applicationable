<?php
namespace Nebo15\LumenApplicationable\Validators;

class AliasValidator
{
    public function validate($attribute, $value)
    {
        return (boolean) preg_match('/^[A-Za-z0-9\-_\.].+$/', $value);
    }
}
