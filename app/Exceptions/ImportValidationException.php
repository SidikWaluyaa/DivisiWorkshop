<?php

namespace App\Exceptions;

use Exception;

class ImportValidationException extends Exception
{
    protected array $errors;

    public function __construct(array $errors, $message = "Import Validation Failed")
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
