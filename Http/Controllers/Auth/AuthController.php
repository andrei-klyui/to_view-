<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ResponseGeneral;

/**
 * @group Authentication
 *
 * ###APIs for managing authentication
 *
 *
 * Class AuthController
 * @package App\Http\Controllers\Auth
 */
class AuthController extends Controller
{
    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * AuthController constructor.
     * @param ResponseGeneral $responseStructured
     */
    public function __construct(
        ResponseGeneral $responseStructured
    ) {
        $this->responseStructured = $responseStructured;
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login/",
     *     tags={"Auth"},
     *     summary="User authorization",
     *     description="User authorization",
     *     @OA\Parameter(
     *        name="email", in="query", required=true, @OA\Schema(type="string"), description="email",
     *     ),
     *     @OA\Parameter(
     *        name="password", in="query", required=true, @OA\Schema(type="string"), description="password",
     *     ),
     *     @OA\Response(
     *        response="200",
     *        description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                       property="status",
     *                       type="boolean",
     *                  ),
     *                  @OA\Property(
     *                      property="metadata",
     *                      type="object",
     *                          @OA\Property(
     *                              property="entity",
     *                              type="object",
     *                              @OA\Property(
     *                                  property="id",
     *                                  type="integer",
     *                              ),
     *                              @OA\Property(
     *                                  property="name",
     *                                  type="string",
     *                              ),
     *                              @OA\Property(
     *                                  property="email",
     *                                  type="string",
     *                              ),
     *                              @OA\Property(
     *                                  property="created_at",
     *                                  type="string",
     *                              ),
     *                              @OA\Property(
     *                                  property="updated_at",
     *                                  type="string",
     *                              ),
     *                              @OA\Property(
     *                                  property="username",
     *                                  type="string",
     *                              ),
     *                              @OA\Property(
     *                                  property="avatar",
     *                                  type="string",
     *                              ),
     *                              @OA\Property(
     *                                  property="avatar_url",
     *                                  type="string",
     *                              ),
     *                              @OA\Property(
     *                                  property="office_id",
     *                                  type="integer",
     *                              ),
     *                              @OA\Property(
     *                                  property="first_name",
     *                                  type="string",
     *                              ),
     *                              @OA\Property(
     *                                  property="last_name",
     *                                  type="string",
     *                              ),
     *                          ),
     *                          @OA\Property(
     *                              property="accessToken",
     *                              type="string",
     *                          ),
     *                          @OA\Property(
     *                              property="tokenType",
     *                              type="string",
     *                          ),
     *                   ),
     *             ),
     *              example={
     *                 "status": true,
     *                  "metadata": {
     *                      "entity": {
     *                          "id": 5,
     *                          "name": "Test",
     *                          "email": "email@example.com",
     *                          "created_at": "2021-08-09 19:45:04",
     *                          "updated_at": "2021-08-09 19:45:04",
     *                          "username": "Test",
     *                          "avatar": null,
     *                          "avatar_url": "",
     *                          "office_id": 1,
     *                          "first_name": "",
     *                          "last_name": ""
     *                      },
     *                      "accessToken": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUz...",
     *                      "tokenType": "Bearer"
     *                  },
     *             }
     *         ),
     *      )
     * )
     */
    public function login(LoginRequest $request)
    {
        return $request->login();
    }


    /**
     * Logout
     *
     * ###Logout user from system.
     *
     * @authenticated
     *
     * @response {
     *  "status": true,
     *  "message": "message success"
     * }
     *
     * @response 401 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     *
     * @param Request $request
     * @return array
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        $this->responseStructured->setStatus(true);
        $this->responseStructured->addMessage(trans('auth.successfully logged out'), 'success');

        return $this->responseStructured->getResponse();
    }
}
