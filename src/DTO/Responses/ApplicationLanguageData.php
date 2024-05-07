<?php

namespace Idynsys\Localizator\DTO\Responses;

class ApplicationLanguageData
{
    // Language code
    public string $code;

    // Language name
    public string $name;

    public function __construct(
        string $code,
        string $name
    ) {
        $this->code = $code;
        $this->name = $name;
    }
}
