<?php

namespace Ids\Localizator\DTO;

use Exception;

abstract class TranslationData
{
    protected string $prefix = 'Localizer';
    protected TranslationTypes $type;

    private string $pathSeparator = '|||';
    public string $languageCode;
    public array $location;
    public $translation;
    private string $languagePath = '';
    private string $path = '';

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
            $this->languagePath = implode(
                $separator ?: $this->pathSeparator,
                array_merge([$this->prefix, $this->languageCode, $this->type], is_null($location) ? $this->location : $location)
            );
        }

        return $this->languagePath;
    }

    public function getKey(): string
    {
        return $this->getLanguagePath();
    }

    public function getParentKey(): string
    {
        return $this->getLanguagePath(null, [$this->getParentName()]);
    }

    public function getLanguageKey(): string
    {
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