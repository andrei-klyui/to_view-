<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\LoginRequest;
use App\Models\Role;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Mail;
use \App\Mail\UserRegistered;
use App\Models\Office;
use App\Events\UserCreated as EventUserCreated;
use App\Http\Responses\ResponseGeneral;
use App\Validators\UserValidator;
use Prettus\Validator\Exceptions\ValidatorException;
use Prettus\Validator\Contracts\ValidatorInterface;
use function Couchbase\defaultDecoder;

/**
 * @group Registration users
 *
 * ###APIs for managing registration users
 *
 *
 * Class RegisterController
 * @package App\Http\Controllers\Auth
 */
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * @var UserValidator
     */
    protected $userValidator;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * UsersController constructor.
     * @param ResponseGeneral $responseStructured
     * @param UserValidator $userValidator
     */
    public function __construct(
        ResponseGeneral $responseStructured,
        UserValidator $userValidator
    )
    {
        $this->responseStructured = $responseStructured;
        $this->userValidator = $userValidator;
        $this->middleware('check.user')->only('register');
    }

    /**
     * Data for registration new user
     *
     * ###Get data for registration new user.
     *
     * @response {
     *  "status": true,
     *  "metadata": "list with 'offices'"
     * }
     *
     *
     * @return array
     */
    public function showRegistrationForm()
    {
        $offices = Office::get();
        $roles = Role::get();

        $this->responseStructured->addMetadata($offices, 'offices');
        $this->responseStructured->addMetadata($roles, 'roles');

        $this->responseStructured->setStatus(true);

        return $this->responseStructured->getResponse();
    }

    /**
     * Registration new user
     *
     * ###Handle a registration request for the application.
     *
     * @bodyParam username              string  required    The username of the user(string;max:255;unique:users). Example: jone
     * @bodyParam name                  string  required    The name of the user(string;max:255). Example: Jone Jone
     * @bodyParam email                 email   required    The email of the user(string;max:255;email;unique:users). Example: email@email.com
     * @bodyParam office_id             int                 The office id of the user(nullable;numeric). Example: 1
     * @bodyParam password              string  required    The password of the user(string;min:6;max:255;confirmed). Example: pass
     * @bodyParam password_confirmation string  required    The password confirmation of the user. Example: pass
     * @bodyParam g-recaptcha-response  string  required    The g-recaptcha-response of the google code recaptcha(required;captcha). Example: kjasdhfuiewgyuhfw...
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
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function register(LoginRequest $request)
    {

        $data = $request->all();

        $requestData = [
            'name' => $data['first_name'],
            'username' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name']
        ];
        $result = array_merge($data, $requestData);

        $this->userValidator->with($result)->passesOrFail(ValidatorInterface::RULE_CREATE);

        $user = User::create($result);
        $role = Role::where('name', Role::ROLE_EMPLOYEE)->firstOrFail()->id;

        $user->roles()->attach($role);

        event(new EventUserCreated($user));

        Mail::to($user->email)->send(new UserRegistered($user));

        $this->guard()->login($user);
        return $request->login();
    }
}
