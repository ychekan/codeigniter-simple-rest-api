<?php

namespace App\Services\Users;

use App\Entities\User;
use App\Enums\UserType;
use App\Models\UserModel;
use App\Services\MainService;
use Carbon\Carbon;
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

    /**
     * @param $id
     * @return array|object|null
     */
    public function getUserById($id)
    {
        return (new UserModel())
            ->where('id', $id)
            ->first();
    }

    /**
     * @param $request
     * @param int $id
     * @return bool
     */
    public function createUserForAdmin($request, int $id = 0): bool
    {
        try {
            $userModel = new UserModel();
            if ($id) {
                $userModel = (new UserModel())
                    ->where('id', $id)
                    ->first();
            }

            $data = [
                "email" => $request->getVar('email') ?? $userModel->email,
                "username" => $request->getVar('username') ?? $userModel->username,
                "name" => $request->getVar('name') ?? $userModel->name,
                "role_id" => $request->getVar('role') ?? $userModel->role_id,
            ];

            if ($request->getVar('password') !== null) {
                $data["password"] = $request->getVar('password');
            }
            if ($request->getVar('is_verified') !== null) {
                $data["verified_at"] = +$request->getVar("is_verified") ? Carbon::now() : null;
            }

            if ($id) {
                return (new UserModel())
                    ->where('id', $id)
                    ->set($data)
                    ->update();
            }

            return $userModel->save($data);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteUserForAdmin(int $id = 0): bool
    {
        return (new UserModel())->delete($id);
    }
}