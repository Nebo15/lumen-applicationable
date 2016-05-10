<?php

namespace Nebo15\LumenApplicationable\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Application extends Model
{

    protected $fillable = ['title', 'description'];

    protected $listable = ['title', 'description'];

    protected $visible = ['_id', 'title', 'description', 'users', 'consumers'];

    protected $casts = ['_id' => 'string'];

    protected function getArrayableRelations()
    {
        return [
            'users' => $this->users,
            'consumers' => $this->consumers,
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

    public function toArray()
    {
        $user_model = config('applicationable.user_model');
        foreach ($this->users as $key => $userObject) {
            $user = $user_model::find($userObject->user_id);
            $this->users[$key]->username = $user->username;
            $this->users[$key]->email = $user->email;
        }
        return parent::toArray();
    }

    public function getUser($user_id)
    {
        $applicationable_user = $this->users()->where('user_id', $user_id)->first();
        if ($applicationable_user) {
            $user_model = config('applicationable.user_model');
            $user = $user_model::find($applicationable_user->user_id);
            $applicationable_user->username = $user->username;
            $applicationable_user->email = $user->email;
        }
        return $applicationable_user;
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
