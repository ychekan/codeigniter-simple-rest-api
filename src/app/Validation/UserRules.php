<?php

namespace App\Validation;

use App\Models\UserModel;
use Exception;

class UserRules
{
    /**
     * Check if this user exist
     * @param string $str
     * @param string $fields
     * @param array $data
     * @return bool
     */
    public function validateUser(string $str, string $fields, array $data): bool
    {
        try {
            $model = new UserModel();
            $user = $model->findUserByEmailAddress($data['email']);
            return password_verify($data['password'], $user['password']);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if this hash existing
     *
     * @param string $str
     * @param string $fields
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function isExistHash(string $str, string $fields, array $data): bool
    {
        return (new UserModel())->existUserByHash($data['hash']);
    }

    /**
     * @param string $str
     * @param string $fields
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function isVerification(string $str, string $fields, array $data): bool
    {
        $user = (new UserModel())->findUserByEmailAddress($data['email']);
        return !!$user['verified_at'];
    }
}