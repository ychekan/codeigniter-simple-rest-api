<?php

namespace App\Filters;

use App\Enums\ApiCode;
use App\Enums\UserType;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Fluent\Auth\Filters\AuthenticationFilter as AuthenticationFilterParent;

class AdminFilter extends AuthenticationFilterParent implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = auth('api')->user();

        if (+$user->role_id != UserType::Admin) {
            return $this->respond([
                'status' => ResponseInterface::HTTP_FORBIDDEN,
                'error' => true,
                'messages' => ApiCode::Forbidden,
                'data' => [
                    'error' => ApiCode::Forbidden
                ]
            ], ResponseInterface::HTTP_FORBIDDEN);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // TODO: Implement after() method.
    }
}