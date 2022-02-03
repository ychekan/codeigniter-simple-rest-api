<?php

namespace App\Filters;

use App\Enums\ApiCode;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use \Fluent\Auth\Filters\AuthenticationFilter as AuthenticationFilterParent;

class AuthenticationFilter extends AuthenticationFilterParent
{

    /**
     * Handle an unauthenticated user.
     *
     * @param RequestInterface $request
     * @param array $guards
     * @return Response
     */
    protected function unauthenticated($request, $guards): Response
    {
        if ($request->isAJAX()) {
            return $this->fail('Unauthenticated.', ResponseInterface::HTTP_UNAUTHORIZED);
        }

        return $this->respond([
            'status' => ResponseInterface::HTTP_UNAUTHORIZED,
            'error' => false,
            'messages' => ApiCode::Unauthorized,
            'data' => [
                'error' => ApiCode::Unauthorized
            ]
        ], ResponseInterface::HTTP_UNAUTHORIZED);
    }
}