<?php

namespace App\Filters;

use App\Enums\ApiCode;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Exception;

class AuthFilter implements FilterInterface
{
    use ResponseTrait;

    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $authenticationHeader = $request->getServer('HTTP_AUTHORIZATION');

        try {
            helper('jwt');
            $encodedToken = getJWTFromRequest($authenticationHeader);
            $user = validateJWTFromRequest($encodedToken);
            $request->setAuth($user->username, $user->username, 'bearer');
            return $request;
        } catch (Exception $e) {
            return Services::response()
                ->setJSON([
                    'status' => ResponseInterface::HTTP_BAD_REQUEST,
                    'error' => false,
                    'messages' => ApiCode::BadRequest,
                    'data' => [
                        'errors' => $e->getMessage()
                    ]
                ])
                ->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
