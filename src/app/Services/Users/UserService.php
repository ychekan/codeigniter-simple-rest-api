<?php

namespace App\Services\Users;

use App\Enums\UserType;
use App\Models\UserModel;
use App\Services\MainService;
use CodeIgniter\Events\Events;
use Fluent\Auth\Contracts\ResetPasswordInterface;

class UserService extends MainService
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
            ->where('role_id', UserType::User)
            ->where('verified_at', null)
            ->where('deleted_at', null)
            ->set([
                'verified_at' => 'NOW()',
                'hash' => null,
            ])
            ->update();
    }

    /**
     * @param $request
     * @return bool
     * @throws \ReflectionException
     */
    public function forgotPassword($request): bool
    {
        $email = $request->getVar('email');
        $userModel = new UserModel();
        $user = $userModel
            ->where('email', $email)
            ->where('verified_at !=', null)
            ->where('deleted_at', null)
            ->asArray()
            ->first();
        if (!$user) {
            return false;
        }
        $hash = $userModel->generateHash();

        $userModel
            ->where('id', $user['id'])
            ->set([
                'hash' => $hash['data']['hash'],
            ])
            ->update();

        return Events::trigger(
            ResetPasswordInterface::class,
            $email,
            $user['name'],
            $hash['data']['hash']
        );
    }

    /**
     * @param $request
     * @return bool
     * @throws \ReflectionException
     */
    public function resetPassword($request): bool
    {
        return (new UserModel())
            ->where('hash', $request->getVar('hash'))
            ->where('verified_at !=', null)
            ->where('deleted_at', null)
            ->set([
                'hash' => null,
                'password' => $request->getVar('password'),
            ])
            ->update();
    }
}