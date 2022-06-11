<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Utils\Helpers\FactoringHelper;
use App\Http\Responses\ResponseGeneral;

/**
 * @group Reset password
 *
 * ###APIs for managing reset password
 *
 *
 * Class ResetPasswordController
 * @package App\Http\Controllers\Auth
 */
class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

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
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Override from original to fix double hash password
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = $password;

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));
    }

    /**
     * Reset password
     *
     * ###Reset the given user's password.
     *
     * @bodyParam email                 email   required    The email of the user(email). Example: email@email.com
     * @bodyParam token                 string  required    The token of the reset password. Example: sdfdsgdfsg...
     * @bodyParam password              string  required    The password of the user(string;min:6;max:255;confirmed). Example: pass
     * @bodyParam password_confirmation string  required    The password confirmation of the user. Example: pass
     *
     * @response {
     *  "status": true,
     *  "entity": "user",
     *  "metadata": "list with 'accessToken', 'tokenType'"
     * }
     *
     * @response 404 {
     *  "status": false,
     *  "message": "errors[]"
     * }
     *
     *
     * @param LoginRequest $request
     * @return array
     */
    public function reset(LoginRequest $request)
    {
        try {
            $validator = $this->validator($request->all());
        }
        catch (\Exception $e) {

            $this->responseStructured->addMessage($e->getMessage(), 'errors');

            return $this->responseStructured->getResponse();
        }

        if($validator->fails())
        {
            $this->responseStructured->addMetadata($request->only('email'));
            $this->responseStructured->addMessage(FactoringHelper::getErrorsMessages($validator), 'errors');

            return $this->responseStructured->getResponse();
        }

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
            $this->resetPassword($user, $password);
        }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($response == Password::PASSWORD_RESET) {
            return $request->login();
        } else {
            $this->responseStructured->addMetadata($request->only('email'));
            $this->responseStructured->addMessage(['email' => trans($response)], 'errors');

            return $this->responseStructured->getResponse();
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, $this->rules(), $this->validationErrorMessages());
    }
}
