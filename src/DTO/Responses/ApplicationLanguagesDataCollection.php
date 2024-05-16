<?php

namespace Idynsys\Localizator\DTO\Responses;

use Generator;
use Idynsys\Localizator\Collections\ApplicationLanguageCollection;

class ApplicationLanguagesDataCollection
{
// данные, полученные по запросу
    private array $originalData;

    // Преобразованные данные в DTO-коллекцию
    private ?ApplicationLanguageCollection $languagesData = null;

    public function __construct(array $data)
    {
        $this->originalData = $data['data'] ?? [];
    }

    public function languages(): Generator
    {
        foreach ($this->originalData as $languageData) {
            yield new ApplicationLanguageData($languageData->code, $languageData->name);
        }
    }

    public function getLanguages(): ApplicationLanguageCollection
    {
        if ($this->languagesData === null) {
            $this->languagesData = new ApplicationLanguageCollection();

            foreach ($this->languages() as $language) {
                $this->languagesData->addItem($language);
            }
        }

        return $this->languagesData;
    }

    public function toArray(): array
    {
        return $this->originalData;
    }
}
