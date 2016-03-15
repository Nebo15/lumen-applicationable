<?php
namespace Nebo15\LumenApplicationable\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class User extends Model
{
    protected $fillable = [ 'user_id', 'role', 'scope'];

    public function __call($method, $parameters)
    {
        if (substr($method, 0, 3) == 'can') {
            return in_array(strtolower(str_replace_first('can', '', $method)), $this->scope);
        }
        return parent::__call($method, $parameters);
    }

    public function isAdmin()
    {
        return $this->role == 'admin';
    }
}
