<?php

namespace Idynsys\Localizator\DTO\Responses;

use Idynsys\Localizator\Enums\TranslationTypes;

/**
 * DTO перевода статического элемента
 */
class StaticTranslationData extends TranslationData
{
    public function __construct(string $product, string $languageCode, array $location = [], string $translation = '')
    {
        parent::__construct(TranslationTypes::STATIC(), $product, $languageCode, $location, $translation);
    }
}
