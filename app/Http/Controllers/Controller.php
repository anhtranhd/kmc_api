<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\BaseService;
/**
 * @OA\Info(
 *   title="Museum API",
 *   version="1.0",
 *   @OA\Contact(
 *     email="museum",
 *     name="Museum Support Team"
 *   )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /** @var BaseService */
    protected $baseService;

    /** @var bool */
    private $success = true;

    /** @var array */
    private $errorMessages = [];

    /** @var null */
    private $successMessages = [];

    /** @var bool */
    private $isForbidden = false;

    /** @var array */
    private $forbiddenMessages = [];

    /** @var null */
    private $message = [];

    /** @var bool */
    private $userFault = false;

    /** @var null */
    private $data = null;

    /** @var array */
    private $pagination = [];

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->baseService = new BaseService();
    }

    protected function respondWithToken($loginData)
    {
        return response()->json([
            'success' => true,
            'message' => 'Login success',
            'data' => [
                'token' => $loginData['token'],
                'token_type' => 'bearer',
                // 'expires_in' => Auth::factory()->getTTL() * 60,
                'user' => $loginData['user']
            ]
        ], 200);
    }

    /**
     * Success response
     *
     * @param  string|array|null  $data
     * @param  array|null  $pagination
     * @param  bool|null  $refreshToken
     *
     * @return JsonResponse
     */
    protected function _successResponse($data = null, $message = null, $pagination = null)
    {
        if ($data !== null) {
            $this->data = $data;
        }

        if ($message !== null) {
            $this->message = $message;
        }

        if ($pagination !== null) {
            $this->pagination = $pagination;
        }

        return $this->buildResponse();
    }

    public function _errorResponse($errors, $message = null)
    {
        $this->success       = false;
        $this->message = $errors;

        return $this->buildResponse();
    }

    /**
     * Return user fault response.
     *
     * @param  array  $errorMessages
     * @param  bool|null  $refreshToken
     *
     * @return JsonResponse
     */
    protected function userErrorResponse($errorMessages)
    {
        $this->success       = false;
        $this->message = $errorMessages;
        
        return $this->buildResponse();
    }

    /**
     * Build the response.
     *
     * @return JsonResponse
     */
    private function buildResponse()
    {
        if (!$this->success) {
            $response = [
                'success'       => $this->success,
                'message' => $this->message
            ];
        } elseif ($this->isForbidden) {
            $response = [
                'isForbidden'       => $this->isForbidden,
                'forbiddenMessages' => $this->forbiddenMessages
            ];
        } else {
            $response = [
                'success' => $this->success
            ];

            if ($this->message !== null) {
                $response['message'] = $this->message;
            }
            
            if ($this->data !== null) {
                $response['data'] = $this->data;
            }
        
            if ($this->pagination != null && count($this->pagination) > 0) {
                $response['pagination'] = $this->pagination;
            }
        }

        return response()->json($response, Response::HTTP_OK);
    }
}
