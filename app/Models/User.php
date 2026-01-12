<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\Access\Authorizable as AuthorizableTrait;

class User implements Authenticatable, Authorizable
{
    use AuthorizableTrait;

    public $uid;
    public $name;
    public $email;
    public $remember_token;

    public function __construct($data = [])
    {
        $this->uid = $data['uid'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->email = $data['email'] ?? null;
    }

    public function getAuthIdentifierName() { return 'uid'; }
    public function getAuthIdentifier() { return $this->uid; }
    public function getAuthPassword() { return null; }
    public function getRememberToken() { return $this->remember_token; }
    public function setRememberToken($value) { $this->remember_token = $value; }
    public function getRememberTokenName() { return 'remember_token'; }
    
    // This was the missing method from earlier
    public function getAuthPasswordName() { return 'password'; }
}