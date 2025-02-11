<?php

namespace App\Helper;

use App\Exception\ValidationException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationHelper{
    private ValidatorInterface $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }


    public static function formatErrors(ConstraintViolationListInterface $errors): string
    {
        $errorMessages = [];

        foreach ($errors as $error) {
            $errorMessages[] = sprintf('%s: %s', $error->getPropertyPath(), $error->getMessage());
        }

        return implode(', ', $errorMessages);
    }

    public function validate($object): void
    {
        $errors = $this->validator->validate($object);

        if (count($errors) > 0) {
            throw new ValidationException($this->formatErrors($errors));
        }
    }
}
