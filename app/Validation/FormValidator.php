<?php

namespace App\Validation;

use App\Exception\FormValidationException;

class FormValidator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->errors = [];
    }

    public function passes()
    {
        foreach ($this->data as $key => $value) {
            if (empty(($value))) {
                $this->errors[$key][] = "{$key} field is required.";
            }

            if (count($this->errors) > 0) {
                throw new FormValidationException;
            }
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

}