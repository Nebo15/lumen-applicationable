<?php
namespace Nebo15\LumenApplicationable\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Consumer extends Model
{
    protected $fillable = [ 'client_id', 'client_secret', 'description', 'scope' ];

    public function __call($method, $parameters)
    {
        if (substr($method, 0, 3) == 'can') {
            return in_array(strtolower(str_replace_first('can', '', $method)), $this->scope);
        }
        return parent::__call($method, $parameters);
    }
}
