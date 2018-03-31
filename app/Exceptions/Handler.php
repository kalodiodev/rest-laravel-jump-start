<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if(($exception instanceof NotFoundHttpException) && ($request->expectsJson())) {
            return response()->json([
                'error' => 'not_found',
                'message' => 'The specified URL could not be found'
            ], 404);
        }

        if(($exception instanceof ModelNotFoundException) && ($request->expectsJson())) {
            $modelName = strtolower(class_basename($exception->getModel()));
            return response()->json([
                'error' => 'not_found',
                'message' => "Could not find {$modelName}"
            ], 404);
        }

        return parent::render($request, $exception);
    }
}
