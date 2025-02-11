<?php

namespace App\Helper;

use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof ValidationException) {
            $response = new JsonResponse(['error' => $exception->getMessage()], $exception->getStatusCode());
        } else {
            $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

            $response = new JsonResponse([
                'error' => $exception->getMessage(),
            ], $statusCode);

        }
        $event->setResponse($response);
    }

}
