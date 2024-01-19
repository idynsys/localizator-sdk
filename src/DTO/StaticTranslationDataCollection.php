<?php

namespace Ids\Localizator\DTO;


class StaticTranslationDataCollection
{
    private array $translations;

    public function __construct(array $data)
    {
        $this->translations = $data['data'];
    }

    public function getTranslations(string $language = null): array
    {
        if (is_null($language)) {
            return $this->translations;
        }
        return $this->translations[$language] ?? [];
    }

    public function translations()
    {
        foreach ($this->translations as $language => &$parents) {
            foreach ($parents as $parentName => &$children) {
                foreach ($children as $childName => $translation) {
                    yield new StaticTranslationData($language, [$parentName, $childName], $translation);
                }
            }
        }
    }
}