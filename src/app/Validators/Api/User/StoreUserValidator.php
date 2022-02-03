<?php

namespace App\Validators\Api\User;

use App\Validators\MainValidator;

class StoreUserValidator extends MainValidator
{
    /**
     * @return string[]
     */
    public static function rules(): array
    {
        return [
            "email" => "required|valid_email|is_unique[users.email]|min_length[6]|max_length[100]",
            "username" => "required|is_unique[users.username]|min_length[6]|max_length[100]",
            "name" => "required|min_length[2]|max_length[100]",
            "password" => "required|min_length[6]|max_length[50]",
            "role" => "integer",
            "is_verified" => "integer",
        ];
    }

    /**
     * @return \string[][]
     */
    public static function messages(): array
    {
        return [
            "email" => [
                "required" => "Email required",
                "valid_email" => "Email address is not in format",
                "is_unique" => "This is email is exist",
                "min_length" => "You can't to use email is less 6 characters",
                "max_length" => "You can't to use email is more 100 characters",
            ],
            "username" => [
                "required" => "Username is required",
                "is_unique" => "This is email is exist",
                "min_length" => "You can't to use email is less 6 characters",
                "max_length" => "You can't to use email is more 100 characters",
            ],
            "name" => [
                "required" => "Name is required",
                "min_length" => "You can't to use email is less 6 characters",
                "max_length" => "You can't to use email is more 100 characters",
            ],
            "password" => [
                "required" => "Password is required",
                "min_length" => "You can't to use password is less 6 characters",
                "max_length" => "You can't to use password is more 50 characters",
            ],
        ];
    }
}