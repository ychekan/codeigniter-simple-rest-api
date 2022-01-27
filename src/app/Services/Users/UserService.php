<?php

namespace App\Services\Users;

use App\Models\UserModel;
use App\Services\MainService;
use Exception;

class UserService extends MainService
{
//    public static function findByEmail($email)
//    {
//        $user = (new UserModel())
//            ->asArray()
//            ->where('email', $email)
//            ->where('verified_at !=', null)
//            ->withDeleted()
//            ->first();
//
//        if (!$user)
//            throw new Exception('User does not exist for specified email address');
//
//        return $user;
//    }
}