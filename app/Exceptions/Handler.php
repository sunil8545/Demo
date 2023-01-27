<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

     /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($request->is('api*')) {
            if($request->is('api/*')){
                if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated.',
                        'data' => [],
                    ], 401);
                }
        
                if($exception instanceof \Illuminate\Validation\ValidationException){
                    return response()->json([
                        'success' => false,
                        'message' => $exception->validator->errors()->first(),
                        'data' => $exception->validator->errors()->all(),
                    ], 422);
                }
        
                if($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException){
                    return response()->json([
                        'success' => false,
                        'message' => 'Resource not found.',
                        'data' => [],
                    ], 404);
                }
        
                if($exception instanceof \Illuminate\Database\QueryException){
                    return response()->json([
                        'success' => false,
                        'message' => $exception->getMessage(),
                        'data' => [],
                    ], 500);
                }
                
                if($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException){
                    return response()->json([
                        'success' => false,
                        'message' => 'URL not found.',
                        'data' => [],
                    ], 404);
                }
            }
        }

        return parent::render($request, $exception);
    }
}
