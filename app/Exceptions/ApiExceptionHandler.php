<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiExceptionHandler
{
    use ApiResponse;

    protected $handlers = [
        ValidationException::class => 'handleValidation',
        ModelNotFoundException::class => 'handleModelNotFound',
        NotFoundHttpException::class => 'handleNotFound',
        AuthenticationException::class => 'handleAuthentication',
    ];

    private function handleValidation(ValidationException $exception)
    {
        foreach ($exception->errors() as $key => $value)
            foreach ($value as $message) {
                $errors[] = [
                    'status' => 422,
                    'message' => $message,
                    'source' => $key
                ];
            }
        return $errors;
    }

    private function handleModelNotFound(ModelNotFoundException $exception)
    {
        return [
            [
                'status' => 404,
                'message' => 'The resource cannot be found.',
                'source' => $exception->getModel() //Might be too much info.
            ]
        ];
    }

    private function handleNotFound(NotFoundHttpException $exception){
        return [
            [
                'status' => 404,
                'message' => 'The resource was not found.',
            ]
        ];
    }

    private function handleAuthentication(AuthenticationException $exception)
    {
        return [
            [
                'status' => 401,
                'message' => 'Unauthenticated.',
            ]
        ];
    }

    public function render($request, Throwable $exception)
    {

        $className = get_class($exception);

        if(array_key_exists($className, $this->handlers)) {
            $method = $this->handlers[$className];
            return $this->error($this->$method($exception));
        }

        $index = strrpos($className, '\\');


        return $this->error([
            [
                'type' => substr($className, $index + 1),
                'status' => 0,
                'message' => $exception->getMessage(),
                'source' => 'Line: ' . $exception->getLine() . ' File: ' . $exception->getFile(), //Too much information to include in an exception.

            ]
        ]);
    }
}
