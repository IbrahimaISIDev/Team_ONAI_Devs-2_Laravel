<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UserRepositoryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'User.repository';
    }
}