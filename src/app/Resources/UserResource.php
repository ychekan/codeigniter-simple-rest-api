<?php

namespace App\Resources;

use Fluent\JWTAuth\Claims\Claim;

class UserResource extends Claim
{
    /**
     * @return array
     */
    public function toArray(): array
    {
        helper('datetime');
        return [
            "id" => $this->getValue()->id,
            "email" => $this->getValue()->email,
            "username" => $this->getValue()->username,
            "name" => $this->getValue()->name,
            "role" => $this->getValue()->role['role'],
            "verified_at" => dateFormatted($this->getValue()->verified_at),
            "created_at" => dateFormatted($this->getValue()->created_at),
            "updated_at" => dateFormatted($this->getValue()->updated_at),
            "deleted_at" => dateFormatted($this->getValue()->deleted_at)
        ];
    }
}