<?php
namespace Nebo15\LumenApplicationable\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['user_id', 'role', 'scope'];

    public function __call($method, $parameters)
    {
        if (substr($method, 0, 3) == 'can') {
            return $this->hasAccess(strtolower(str_replace_first('can', '', $method)));
        }
        if (substr($method, 0, 2) == 'is') {
            return $this->hasRole(strtolower(str_replace_first('is', '', $method)));
        }

        return parent::__call($method, $parameters);
    }

    public function hasAccess($scope)
    {
        return in_array($scope, $this->scope);
    }

    public function hasRole($role)
    {
        return $this->role == $role;
    }

}
