<?php
namespace Nebo15\LumenApplicationable;

class Validator
{
    public function alias($attribute, $value)
    {
        return (boolean)preg_match('/^[A-Za-z0-9\-_\.].+$/', $value);
    }

    public function requiredScopes($attribute, $value)
    {
        return config('applicationable.required_scopes.users') == array_intersect(
            config('applicationable.required_scopes.users'),
            $value
        );
    }
}
