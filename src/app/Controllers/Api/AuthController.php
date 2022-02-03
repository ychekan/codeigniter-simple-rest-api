<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Enums\ApiCode;
use App\Resources\UserResource;
use App\Validators\Api\Auth\ConfirmValidator;
use App\Validators\Api\Auth\LoginValidator;
use App\Validators\Api\Auth\RegisterValidator;
use App\Validators\Api\Auth\ForgotPasswordValidator;
use App\Validators\Api\Auth\ResetPasswordValidator;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use ReflectionException;

class AuthController extends BaseController
{
    use ResponseTrait;

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
     * @throws ReflectionException
     */
    public function register()
    {
        $rules = RegisterValidator::rules();
        $messages = RegisterValidator::messages();

        if ($this->validate($rules, $messages) && $this->services->userService()->createSimpleUser($this->request)) {
            return $this->respondCreated([
                'status' => ResponseInterface::HTTP_CREATED,
                "error" => false,
                'messages' => ApiCode::Created,
                'data' => [
                    'messages' => 'Successfully, user has been registered',
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
     * Confirmation flow
     *
     * @return Response
     * @throws ReflectionException
     */
    public function confirm(): Response
    {
        $rules = ConfirmValidator::rules();
        $messages = ConfirmValidator::messages();

        $hash = $this->request->getVar('hash');

        if ($this->validate($rules, $messages) && $this->services->userService()->verifyUser($hash)) {
            return $this->respondCreated([
                'status' => ResponseInterface::HTTP_OK,
                "error" => false,
                'messages' => ApiCode::Created,
                'data' => [
                    'messages' => 'User is confirmed successfully',
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
     * Get a JWT via given credentials.
     *
     * @return \CodeIgniter\Http\Response
     */
    public function login(): Response
    {
        $rules = LoginValidator::rules();
        $messages = LoginValidator::messages();

        $credentials = [
            'email' => $this->request->getVar('email'),
            'password' => $this->request->getVar('password')
        ];


        if ($this->validate($rules, $messages) && $token = auth('api')->attempt($credentials)) {
            return $this->respond([
                'status' => ResponseInterface::HTTP_CREATED,
                "error" => true,
                'messages' => ApiCode::Created,
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                ]
            ], ResponseInterface::HTTP_CREATED);
        }

        return $this->respond([
            'status' => ResponseInterface::HTTP_UNAUTHORIZED,
            "error" => true,
            'messages' => ApiCode::UnprocessableEntity,
            'data' => [
                'errors' => $this->validator->getErrors(),
            ]
        ], ResponseInterface::HTTP_UNAUTHORIZED);
    }

    /**
     * Get the authenticated User.
     *
     * @return \CodeIgniter\Http\Response
     */
    public function profile(): Response
    {
        $profile = auth('api')->user();

        if ($profile) {
            return $this->respond([
                'status' => ResponseInterface::HTTP_OK,
                'error' => false,
                'messages' => ApiCode::OK,
                'data' => [
                    'user' => new UserResource($profile)
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

    /**
     * Log the user out (Invalidate the token).
     *
     * @return Response
     */
    public function logout(): Response
    {
        auth('api')->logout();

        return $this->respond([
            'status' => ResponseInterface::HTTP_OK,
            'error' => false,
            'messages' => ApiCode::OK,
            'data' => [
                'message' => 'logout'
            ]
        ], ResponseInterface::HTTP_OK);
    }

    /**
     * Refresh a token.
     *
     * @return \CodeIgniter\Http\Response
     */
    public function refresh(): Response
    {
        return $this->respond([
            'status' => ResponseInterface::HTTP_OK,
            "error" => true,
            'messages' => ApiCode::Created,
            'data' => auth('api')->refresh()
        ], ResponseInterface::HTTP_OK);
    }

    /**
     * @return Response
     * @throws ReflectionException
     */
    public function forgotPassword(): Response
    {
        $rules = ForgotPasswordValidator::rules();
        $messages = ForgotPasswordValidator::messages();

        if ($this->validate($rules, $messages) && $this->services->userService()->forgotPassword($this->request)) { // send email for reset password
            return $this->respond([
                'status' => ResponseInterface::HTTP_OK,
                "error" => true,
                'messages' => ApiCode::OK,
                'data' => [
                    'message' => 'Email is send'
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

    /**
     * @return Response
     * @throws ReflectionException
     */
    public function resetPassword(): Response
    {
        $rules = ResetPasswordValidator::rules();
        $messages = ResetPasswordValidator::messages();

        if ($this->validate($rules, $messages) && $this->services->userService()->resetPassword($this->request)) { // send email for reset password
            return $this->respond([
                'status' => ResponseInterface::HTTP_OK,
                "error" => true,
                'messages' => ApiCode::OK,
                'data' => [
                    'message' => 'Password is update!'
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
}
