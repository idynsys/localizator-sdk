<?php

namespace Idynsys\Localizator\Exceptions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Throwable;

class ExceptionHandler
{
    private Throwable $throwable;

    public function __construct(Throwable $throwable)
    {
        $this->throwable = $throwable;
    }

    public function handle(): LocalizerSdkException
    {
        return new LocalizerSdkException($this->getMessage(), $this->getCode(), $this->throwable);
    }

    protected function getMessage(): string
    {
        return $this->throwable->getMessage();
    }

    protected function getCode(): int
    {
    $code = 500;
        switch (get_class($this->throwable)) {
            case ClientException::class:
                $code = $this->throwable->getResponse()->getStatusCode();
                break;
            case ConnectException::class:
                $code = 503;
                break;
        }

        return $code;
    }
}