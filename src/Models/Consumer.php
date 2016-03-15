<?php
namespace Nebo15\LumenApplicationable\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Consumer extends Model
{
    protected $fillable = [ 'client_id', 'client_secret', 'description' ];
}
