<?php

namespace Ids\Localizator\DTO;

class StaticTranslationData extends TranslationData
{
    public function __construct(string $languageCode, array $location = [], string $translation = '')
    {
        parent::__construct($languageCode, $location, $translation);

        $this->type = TranslationTypes::STATIC();
    }
}