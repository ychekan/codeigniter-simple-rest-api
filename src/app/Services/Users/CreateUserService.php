<?php

namespace App\Services\Users;

use App\Controllers\User;
use App\Models\UserModel;
use App\Services\MainService;
use Config\Services;

class CreateUserService extends MainService
{
    /**
     * @param $request
     * @return bool
     * @throws \ReflectionException
     */
    public function createSimpleUser($request): bool
    {
        $userModel = new UserModel();

        $data = [
            "email" => $request->getVar("email"),
            "username" => $request->getVar("username"),
            "name" => $request->getVar("name"),
            "password" => $request->getVar("password"),
        ];

        return $userModel->save($data);
    }

    /**
     * @throws \ReflectionException
     */
    public function verifyUser($hash): bool
    {
        return (new UserModel())
            ->where('hash', $hash)
            ->where('verified_at', null)
            ->where('deleted_at', null)
            ->set([
                'verified_at' => 'NOW()',
                'hash' => '',
            ])
            ->update();
    }
}