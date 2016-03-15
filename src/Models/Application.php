<?php

namespace Nebo15\LumenApplicationable\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Nebo15\REST\Traits\ListableTrait;
use Nebo15\REST\Interfaces\ListableInterface;

class Application extends Model implements ListableInterface
{
    use ListableTrait;

    protected $fillable = ['title', 'description'];

    protected $listable = ['title', 'description'];

    protected $visible = ['_id', 'title', 'description', 'users', 'consumers'];

    protected function getArrayableRelations()
    {
        return [
            'users' => $this->users,
            'consumers' => $this->consumers
        ];
    }

    public function users()
    {
        return $this->embedsMany('Nebo15\LumenApplicationable\Models\User');
    }

    public function consumers()
    {
        return $this->embedsMany('Nebo15\LumenApplicationable\Models\Consumer');
    }

    public function getUser($user_id)
    {
        return $this->users()->where('user_id', $user_id)->first();
    }

    public function setUser($data)
    {
        $token = ($data instanceof User) ? $data : new User($data);
        $this->users()->associate($token);
        return $this;
    }

    public function deleteUser($user_id)
    {
        $this->users()->dissociate($this->getUser($user_id));
        return $this;
    }

    public function getConsumer($client_id)
    {
        return $this->consumers()->where('client_id', $client_id)->first();
    }

    public function setConsumer($data)
    {
        $token = ($data instanceof Consumer) ? $data : new Consumer($data);
        $this->consumers()->associate($token);
        return $this;
    }

    public function deleteConsumer($client_id)
    {
        $this->consumers()->dissociate($this->getConsumer($client_id));
        return $this;
    }

}
