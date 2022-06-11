<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\ResponseGeneral;
use Illuminate\Http\Request;

use Closure;

class CheckUser
{
    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    public function __construct(ResponseGeneral $responseStructured)
    {
        $this->responseStructured = $responseStructured;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = User::where('email', '=', $request->email)->first();

        if ($user != null) {

            return response($this->responseStructured
                ->addMessage(trans('auth.dublicate'), 'success')
                ->setStatus(false)
                ->getResponse(), 422);

        }

        return $next($request);
    }
}
