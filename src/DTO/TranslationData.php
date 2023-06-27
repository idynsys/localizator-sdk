<?php

namespace Ids\Localizator\DTO;

use Exception;
use Ids\Localizator\Cache\CacheStorageTypes;

abstract class TranslationData
{
    protected string $prefix = 'Localizer';
    protected TranslationTypes $type;

    protected string $pathSeparator = '|||';
    protected string $languageCode;
    protected array $location;
    protected $translation;
    protected string $languagePath = '';
    protected string $path = '';

    public function __construct(string $languageCode, array $location = [], $translation = '', ?string $prefix = null)
    {
        $this->languageCode = $languageCode;
        $this->location = $location;
        $this->translation = $translation;

        if ($prefix) {
            $this->prefix = $prefix;
        }
    }

    public function getPath(?string $separator = null): string
    {
        if (empty($this->path) || !is_null($separator)) {
            $this->path = implode(
                $separator ?: $this->pathSeparator,
                array_merge([$this->type], $this->location)
            );
        }

        return $this->path;
    }

    public function getLanguagePath(?string $separator = null, ?array $location = null): string
    {
        if (empty($this->languagePath) || !is_null($separator)) {
            if (is_null($location)) {
                $location = $this->location ?: [];
            }

            $this->languagePath = implode(
                $separator ?: $this->pathSeparator,
                array_merge([$this->prefix, $this->languageCode, $this->type], $location)
            );
        }

        return $this->languagePath;
    }

    public function getKey(CacheStorageTypes $storageType): string
    {
        switch ($storageType->getValue()) {
            case CacheStorageTypes::TRANSLATIONS_STORAGE_TYPE:
                return $this->getLanguagePath();
            case CacheStorageTypes::PARENTS_STORAGE_TYPE:
                return $this->getLanguagePath(null, [$this->getParentName()]);
        }

        return $this->getLanguagePath(null, []);
    }

    public function getParentName(): string
    {
        if (count($this->location) === 0) {
            throw new Exception('There is not parent level in this translation data.');
        }

        return reset($this->location);
    }

    public function getItemName(): string
    {
        if (count($this->location) <= 1) {
            throw new Exception('There is not item level in this translation data.');
        }

        return end($this->location);
    }

    public function setTranslation($translation): void
    {
        $this->translation = $translation;
    }

    public function getTranslation()
    {
        return $this->translation;
    }
}