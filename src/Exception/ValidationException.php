<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends HttpException
{
    public function __construct(string $message, int $statusCode = 400)
    {
        parent::__construct($statusCode, $message);
    }

}
