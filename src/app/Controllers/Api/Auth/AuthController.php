<?php

namespace App\Controllers\Api\Auth;

use App\Controllers\BaseController;
use App\Enums\ApiCode;
use App\Models\UserModel;
use App\Validators\Api\Auth\LoginValidator;
use App\Validators\Api\Auth\RegisterValidator;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use Exception;
use Firebase\JWT\JWT;

class AuthController extends BaseController
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

    /**
     * @return Response|ResponseInterface|mixed
     * @throws Exception
     */
    public function login()
    {
        $rules = LoginValidator::rules();
        $messages = LoginValidator::messages();

        if (!$this->validate($rules, $messages)) {
            return $this->respond([
                'status' => ResponseInterface::HTTP_UNPROCESSABLE_ENTITY,
                'error' => false,
                'messages' => ApiCode::UnprocessableEntity,
                'data' => [
                    'errors' => $this->validator->getErrors()
                ]
            ], ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
        }
        $input = $this->getRequestInput($this->request);

//        return $this->respondCreated([
//            'status' => ResponseInterface::HTTP_CREATED,
//            'error' => false,
//            'messages' => ApiCode::Created,
//            'data' => [
//                'access_token' => $this->getJWTForUser($input['email'])
//            ]
//        ]);

        $key = Services::getSecretKey();

        $userModel = new UserModel();

        $userdata = $userModel->findUserByEmailAddress($this->request->getVar("email"));

        $iat = time(); // current timestamp value
        $nbf = $iat + 10;
        $exp = $iat + 3600;

        $payload = array(
            "iss" => "The_claim",
            "aud" => "The_Aud",
            "iat" => $iat, // issued at
            "nbf" => $nbf, //not before in seconds
            "exp" => $exp, // expire time in seconds
            "data" => $userdata,
        );

        $token = JWT::encode($payload, $key);

        $response = [
            'status' => 200,
            'error' => false,
            'messages' => 'User logged In successfully',
            'data' => [
                'token' => $token
            ]
        ];
        return $this->respondCreated($response);
    }

    /**
     * @param string $emailAddress
     * @param int $responseCode
     * @return string
     * @throws Exception
     */
    private function getJWTForUser(string $emailAddress, int $responseCode = ResponseInterface::HTTP_OK)
    {
        try {
            $model = new UserModel();
            $user = $model->findUserByEmailAddress($emailAddress);
            unset($user['password']);

            helper('jwt');

            return getSignedJWTForUser($emailAddress);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
