<?php

namespace Idynsys\Localizator\DTO;

use Idynsys\Localizator\Enums\TranslationTypes;

/**
 * DTO перевода статического элемента
 */
class StaticTranslationData extends TranslationData
{
    public function __construct(string $product, string $languageCode, array $location = [], string $translation = '')
    {
        parent::__construct($product, $languageCode, $location, $translation);

        $this->type = TranslationTypes::STATIC();
    }
}