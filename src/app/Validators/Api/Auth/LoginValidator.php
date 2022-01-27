<?php

namespace App\Validators\Api\Auth;

use App\Validators\MainValidator;

class LoginValidator extends MainValidator
{
    /**
     * @return string[]
     */
    public static function rules(): array
    {
        return [
            "email" => "required|valid_email|min_length[6]|max_length[100]",
            "password" => "required|min_length[6]|max_length[50]|validateUser[email, password]",
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
            "password" => [
                "required" => "Password is required",
                "min_length" => "You can't to use password is less 6 characters",
                "max_length" => "You can't to use password is more 50 characters",
                'validateUser' => 'Invalid login credentials provided'
            ],
        ];
    }
}