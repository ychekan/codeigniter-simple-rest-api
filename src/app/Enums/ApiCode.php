<?php

namespace App\Enums;

class ApiCode
{
    // 200
    const OK = 'OK';

    // 201
    const Created = 'CREATED';

    // 204
    const NoContent = 'NO_CONTENT';

    // 400
    const BadRequest = 'BAD_REQUEST';

    // 401
    const Unauthorized = 'UNAUTHORIZED';

    // 403
    const Forbidden = 'FORBIDDEN';

    // 404
    const NotFound = 'NOT_FOUND';

    // 409
    const Conflict = 'CONFLICT';

    // 422
    const UnprocessableEntity = 'UNPROCESSABLE_ENTITY';

    // 500
    const InternalServerError = 'INTERNAL_SERVER_ERROR';
}