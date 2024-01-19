<?php

namespace Ids\Localizator\DTO\Responses;

class TokenData
{
    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }
}