<?php

namespace App\Controllers;

use App\Enums\ApiCode;
use App\Models\UserModel;

use App\Validators\Api\RegisterValidator;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\Response;
use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use Exception;
use \Firebase\JWT\JWT;

class User extends ResourceController
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
     * Registration flow
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function register()
    {
        $rules = RegisterValidator::rules();
        $messages = RegisterValidator::messages();

        if ($this->validate($rules, $messages) && $this->services->createUserService()->createSimpleUser($this->request)) {
            return $this->respondCreated([
                'status' => Response::HTTP_CREATED,
                "error" => false,
                'messages' => ApiCode::Created,
                'data' => [
                    'messages' => 'Successfully, user has been registered',
                ]
            ]);
        }
        return $this->respond([
            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            "error" => true,
            'messages' => ApiCode::UnprocessableEntity,
            'data' => [
                'errors' => $this->validator->getErrors(),
            ]
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param $hash
     * @return RedirectResponse
     */
    public function verify($hash): RedirectResponse
    {
        if ($this->services->createUserService()->verifyUser($hash)) {
            return redirect()->to('/');
        }
        return redirect()->to('404');
    }
}
