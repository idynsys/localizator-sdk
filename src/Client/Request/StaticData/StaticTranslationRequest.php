<?php

namespace Ids\Localizator\Client\Request\StaticData;

class StaticTranslationRequest
{

    // Application Secret Key
    protected string $applicationSecretKey;

    protected ?string $languageCode;

    public function __construct(
        ?string $languageCode = null
    ) {
        $this->languageCode = $languageCode;
    }

    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }

    public function getJsonParameters()
    {
        return null;
    }

    public function getHeaders(): array
    {
        return ['application-key' => $this->applicationSecretKey];
    }
}