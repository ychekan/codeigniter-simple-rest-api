<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Enums\ApiCode;
use App\Resources\UserResource;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class UsersController extends BaseController
{
    /**
     * @var Services
     */
    public $services;

    /**
     */
    public function __construct()
    {
        $this->services = new Services();
        helper('url');
    }

//    /**
//     * @return Response
//     */
//    public function create(): Response
//    {
//
//    }
//
//    public function update(): Response
//    {
//
//    }
//
//    public function delete(): Response
//    {
//
//    }

    /**
     * @param null $id
     * @return Response
     */
    public function show($id = 0): Response
    {
        dump('show', $id);exit();
        $user = $this->services->userService()->getUserById($id);

        if ($user) {
            return $this->respond([
                'status' => ResponseInterface::HTTP_OK,
                'error' => false,
                'messages' => ApiCode::OK,
                'data' => [
                    'user' => new UserResource($user)
                ]
            ], ResponseInterface::HTTP_OK);
        }

        return $this->respond([
            'status' => ResponseInterface::HTTP_NOT_FOUND,
            'error' => true,
            'messages' => ApiCode::NotFound,
            'data' => [
                'errors' => 'User not found'
            ]
        ], ResponseInterface::HTTP_NOT_FOUND);
    }
}
