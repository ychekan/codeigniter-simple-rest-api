<?php

namespace App\Validators\Api\Auth;

use App\Validators\MainValidator;

class ConfirmValidator extends MainValidator
{
    /**
     * @return string[]
     */
    public static function rules(): array
    {
        return [
            "hash" => "required|isExistHash[hash]|min_length[32]|max_length[32]",
        ];
    }

    /**
     * @return \string[][]
     */
    public static function messages(): array
    {
        return [
            "hash" => [
                "required" => "Hash required",
                "isExistHash" => "Hash is not exist",
                "min_length" => "You can't to use email is less 6 characters",
                "max_length" => "You can't to use email is more 100 characters",
            ],
        ];
    }
}