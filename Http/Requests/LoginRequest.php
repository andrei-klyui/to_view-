<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\ResponseGeneral;

/**
 * Class LoginRequest
 * @package App\Http\Requests
 */
class LoginRequest extends FormRequest
{
    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * LoginRequest constructor.
     * @param ResponseGeneral $responseStructured
     * @param array $query
     * @param array $request
     * @param array $attributes
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param null $content
     */
    public function __construct(
        ResponseGeneral $responseStructured,
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $this->responseStructured = $responseStructured;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Authorizes the user
     *
     * @return bool|\Laravel\Passport\PersonalAccessTokenResult
     */
    public function authorizeUser()
    {
        $credentials = $this->only(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return false;
        }

        $user = $this->user();

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

        return $tokenResult;
    }

    /**
     * @return array
     */
    public function login()
    {
        $token = $this->authorizeUser();

        if (!$token) {

            $this->responseStructured->addMessage(trans('auth.failed'), 'errors');

            return response()->json($this->responseStructured->getResponse(), 401);
        }

        $this->responseStructured->setStatus(true);
        $this->responseStructured->addEntity($this->user());
        $this->responseStructured->addMetadata($token->accessToken, 'accessToken');
        $this->responseStructured->addMetadata('Bearer', 'tokenType');

        return $this->responseStructured->getResponse();
    }
}
