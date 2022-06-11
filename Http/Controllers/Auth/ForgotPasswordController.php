<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Responses\ResponseGeneral;

/**
 * @group Forgot password
 *
 * ###APIs for managing forgot password
 *
 *
 * Class ForgotPasswordController
 * @package App\Http\Controllers\Auth
 */
class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * ForgotPasswordController constructor.
     * @param ResponseGeneral $responseStructured
     */
    public function  __construct(ResponseGeneral $responseStructured)
    {
        $this->responseStructured = $responseStructured;
    }

    /**
     * Send a reset link
     *
     * ###Send a reset link to the given user.
     *
     * @bodyParam email         email   required    The email of the user(email). Example: email@email.com
     *
     * @response {
     *  "status": true,
     *  "message": "message success"
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     *
     * @param Request $request
     * @return array
     */
    public function sendResetLinkEmail(Request $request)
    {
        try {
            $this->validateEmail($request);
        } catch (\Exception $e) {

            $this->responseStructured->addMessage(
                trans('validation.email', ['attribute' => 'email']),
                'errors');

            return $this->responseStructured->getResponse();
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            $this->responseStructured->setStatus(true);
            $this->responseStructured->addMessage(trans($response), 'success');
        } else {
            $this->responseStructured->addMetadata($request->only('email'));
            $this->responseStructured->addMessage(['email' => trans($response)], 'errors');
        }

        return $this->responseStructured->getResponse();
    }

}
