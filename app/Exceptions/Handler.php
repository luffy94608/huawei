<?php

namespace App\Exceptions;

use App\Models\ApiResult;
use App\Models\ErrorEnum;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        } elseif ($e instanceof UnauthorizedHttpException) {
            return response()->json((new ApiResult(ErrorEnum::InvalidToken, ErrorEnum::transform(ErrorEnum::InvalidToken), ''))->toJson());
        } elseif ($e instanceof TokenExpiredException) {
            return response()->json((new ApiResult(ErrorEnum::TokenExpired, ErrorEnum::transform(ErrorEnum::TokenExpired), ''))->toJson());
        } elseif ($e instanceof TokenBlacklistedException) {
            return response()->json((new ApiResult(ErrorEnum::InvalidToken, ErrorEnum::transform(ErrorEnum::InvalidToken), ''))->toJson());
        } elseif ($e instanceof JWTException) {
            return response()->json((new ApiResult(ErrorEnum::InvalidToken, ErrorEnum::transform(ErrorEnum::InvalidToken), ''))->toJson());
        }

        return parent::render($request, $e);
    }
}
