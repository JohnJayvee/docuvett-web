<?php

namespace App\Resolvers;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserResolver implements \OwenIt\Auditing\Contracts\UserResolver
{
    /**
     * {@inheritdoc}
     */
    public static function resolve(): ?JWTSubject
    {
        return JWTAuth::user() ?: null;
    }
}
