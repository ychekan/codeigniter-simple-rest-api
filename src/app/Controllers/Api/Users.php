<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Enums\ApiCode;
use App\Resources\UserResource;
use App\Validators\Api\User\StoreUserValidator;
use App\Validators\Api\User\UpdateUserValidator;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class Users extends BaseController
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

    /**
     * @param null $id
     * @return Response
     */
    public function show($id = 0): Response
    {
        $user = $this->services->userService()->getUserById($id);

        if ($user) {
            return $this->respond([
                'status' => ResponseInterface::HTTP_OK,
                'error' => false,
                'messages' => ApiCode::OK,
                'data' => new UserResource($user)
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

    /**
     * @return Response
     */
    public function store(): Response
    {
        $rules = StoreUserValidator::rules();
        $messages = StoreUserValidator::messages();

        if ($this->validate($rules, $messages) && $this->services->userService()->createUserForAdmin($this->request)) {
            return $this->respondCreated([
                'status' => ResponseInterface::HTTP_CREATED,
                "error" => false,
                'messages' => ApiCode::Created,
                'data' => [
                    'messages' => 'Successfully, user has been created',
                ]
            ]);
        }
        return $this->respond([
            'status' => ResponseInterface::HTTP_UNPROCESSABLE_ENTITY,
            "error" => true,
            'messages' => ApiCode::UnprocessableEntity,
            'data' => [
                'errors' => $this->validator->getErrors(),
            ]
        ], ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function update($id = 0): Response
    {
        $rules = UpdateUserValidator::rules();
        $messages = UpdateUserValidator::messages();

        if ($this->validate($rules, $messages) && $this->services->userService()->createUserForAdmin($this->request, $id)) {
            return $this->respond([
                'status' => ResponseInterface::HTTP_OK,
                "error" => false,
                'messages' => ApiCode::OK,
                'data' => [
                    'messages' => 'Successfully, user has been updated',
                ]
            ], ResponseInterface::HTTP_OK);
        }
        return $this->respond([
            'status' => ResponseInterface::HTTP_UNPROCESSABLE_ENTITY,
            "error" => true,
            'messages' => ApiCode::UnprocessableEntity,
            'data' => [
                'errors' => $this->validator->getErrors(),
            ]
        ], ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function delete($id = 0): Response
    {
        if ($this->services->userService()->deleteUserForAdmin($id)) {
            return $this->respond([
                'status' => ResponseInterface::HTTP_OK,
                "error" => false,
                'messages' => ApiCode::OK,
                'data' => [
                    'messages' => 'Successfully deleted user',
                ]
            ], ResponseInterface::HTTP_OK);
        }
        return $this->respond([
            'status' => ResponseInterface::HTTP_UNPROCESSABLE_ENTITY,
            "error" => true,
            'messages' => ApiCode::UnprocessableEntity,
            'data' => [
                'errors' => "Can't delete this user, maybe later",
            ]
        ], ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
    }
}
