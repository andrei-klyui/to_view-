<?php

namespace App\Exceptions;

use App\Http\Responses\ResponseGeneral;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Prettus\Validator\Exceptions\ValidatorException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Handler.
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler
{
    /**
     * @var ResponseGeneral
     */
    protected $responseStructured;

    /**
     * Create a new exception handler instance.
     *
     * Handler constructor.
     * @param Container $container
     * @param ResponseGeneral $responseStructured
     */
    public function __construct(
        Container $container,
        ResponseGeneral $responseStructured
    ) {
        $this->responseStructured = $responseStructured;

        parent::__construct($container);
    }

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Exception $exception
     * @return mixed|void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $exception
     * @return JsonResponse|Response
     */
    public function render($request, Exception $exception)
    {
        $status = Response::HTTP_NOT_FOUND;

        switch (true) {
            case $exception instanceof PostTooLargeException:
                $status = Response::HTTP_BAD_REQUEST;
                $this->responseStructured->addMessage(trans('validation.max_post'), 'errors');
                break;
            case $exception instanceof AuthorizationException:
                $status = Response::HTTP_UNAUTHORIZED;
                $this->responseStructured->addMessage(trans('exception.'.$status), 'errors');
                break;
            case $exception instanceof NotFoundHttpException:
                $status = Response::HTTP_NOT_FOUND;
                $this->responseStructured->addMessage(trans('exception.'.$status), 'errors');
                break;
            case $exception instanceof ModelNotFoundException:
                $status = Response::HTTP_UNPROCESSABLE_ENTITY;
                $this->responseStructured->addMessage(
                    [
                        trans('exception.'.$status),
                        ['id' => $exception->getIds(), 'model' => $exception->getModel()],
                        ['status code' => $status],
                    ],
                    'errors'
                );
                break;
            case $exception instanceof ValidatorException:
                $status = Response::HTTP_BAD_REQUEST;
                $this->responseStructured->addMessage(
                    [
                        $exception->getMessageBag(),
                    ],
                    'errors'
                );
                break;
            case $exception instanceof ValidationException:
                $status = Response::HTTP_BAD_REQUEST;
                $this->responseStructured->addMessage(
                    [
                        trans('exception.'.$status),
                        $exception->errors(),
                        ['status code' => $status],
                    ],
                    'errors'
                );
                break;
            default:
                $this->responseStructured->addMessage($exception->getMessage(), 'errors');
        }

        return response()->json($this->responseStructured->getResponse(), $status);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return array|Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $this->responseStructured->addMessage(trans('auth.'.$exception->getMessage()), 'errors');

        return response()->json($this->responseStructured->getResponse(), 401);
    }
}
