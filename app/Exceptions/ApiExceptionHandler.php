<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiExceptionHandler
{
    use ApiResponseTrait;

    /**
     * Handle authentication exceptions (401).
     */
    public static function handleAuthentication(AuthenticationException $e): JsonResponse
    {
        return static::unauthorized('Authentication required');
    }

    /**
     * Handle validation exceptions (422).
     */
    public static function handleValidation(ValidationException $e): JsonResponse
    {
        return self::unprocessable($e->errors());
    }

    /**
     * Handle authorization exceptions (403).
     */
    public static function handleAuthorization(AuthorizationException $e): JsonResponse
    {
        return static::forbidden('Access denied');
    }

    /**
     * Handle model not found exceptions (404).
     */
    public static function handleModelNotFound(ModelNotFoundException $e): JsonResponse
    {
        $model = strtolower(class_basename($e->getModel()));
        $ids = $e->getIds();

        return static::notFound("The requested {$model} could not be found.");
    }

    /**
     * Handle route not found exceptions (404).
     */
    public static function handleRouteNotFound(NotFoundHttpException $e): JsonResponse
    {
        return static::notFound('Endpoint not found');
    }

    /**
     * Handle rate limiting exceptions (429).
     */
    public static function handleThrottleRequests(ThrottleRequestsException $e): JsonResponse
    {
        return static::tooManyRequests('Too many requests');
    }
}
