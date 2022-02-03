<?php

namespace App\Validators\Api\Auth;

use App\Validators\MainValidator;

class ForgotPasswordValidator extends MainValidator
{
    /**
     * @return string[]
     */
    public static function rules(): array
    {
        return [
            "email" => "required|valid_email|min_length[6]|max_length[100]|isVerification[email]",
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
                "isVerification" => "Email address is not verification",
                "min_length" => "You can't to use email is less 6 characters",
                "max_length" => "You can't to use email is more 100 characters",
            ],
        ];
    }
}