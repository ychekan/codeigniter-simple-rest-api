<?php

namespace App\Controllers\Api;

use App\Enums\ApiCode;
use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use Exception;

class UserController extends ResourceController
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

    public function profile($request)
    {
        var_dump($request);
        exit();
        $key = Services::getSecretKey();

        $authHeader = $this->request->getHeader("Authorization");
        $authHeader = $authHeader->getValue();
        $token = $authHeader;

        try {
            $decoded = JWT::decode($token, $key, array("HS256"));

            if ($decoded) {
                return $this->respondCreated([
                    'status' => 200,
                    'error' => false,
                    'messages' => 'User details',
                    'data' => [
                        'profile' => $decoded,
                    ]
                ]);
            }
        } catch (Exception $ex) {
            return $this->respondCreated([
                'status' => 401,
                'error' => true,
                'messages' => ApiCode::Forbidden,
                'data' => [
                    'errors' => $ex->getMessage()
                ]
            ]);
        }
    }
}
