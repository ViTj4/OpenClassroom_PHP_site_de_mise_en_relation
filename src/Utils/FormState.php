<?php

class FormState
{
    private array $errors = [];

    public function __construct(
        private readonly array $values = []
    ) {
    }

    public static function fromArray(array $source, array $fields): FormState
    {
        $values = [];

        foreach ($fields as $field) {
            $values[$field] = trim($source[$field] ?? '');
        }

        return new FormState($values);
    }

    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getValue(string $field): string
    {
        return $this->values[$field] ?? '';
    }
}
