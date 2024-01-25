<?php

namespace Idynsys\Localizator\Exceptions;

use Exception;
use Throwable;

abstract class RequestException extends Exception
{
    private ?array $originalError = null;

    public function __construct(array $errorData, $code = 0, Throwable $previous = null)
    {
        $this->originalError = $errorData;
        parent::__construct($this->getErrorMessage(), $code, $previous);
    }

    private function getErrorMessage(): string
    {
        return json_encode($this->originalError);
    }

    public function getErrorCode(): int
    {
        return $this->getCode();
    }

    public function getError(): array
    {
        return $this->originalError ?: ['error' => $this->getMessage()];
    }

    public function getOriginalMessage()
    {
        return $this->originalError;
    }
}