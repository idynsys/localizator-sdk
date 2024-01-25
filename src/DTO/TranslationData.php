<?php

namespace Ids\Localizator\DTO;

use Ids\Localizator\Cache\CacheStorageTypes;
use Ids\Localizator\Enums\TranslationTypes;
use RuntimeException;

/**
 * DTO для данных перевода одного элемента
 */
abstract class TranslationData
{
    // префикс, используется для ключей кэширования
    protected string $prefix = 'Localizer';

    // тип элемента
    protected TranslationTypes $type;

    // наименование продукта
    protected string $product;

    // Разделитель в ключе переводов
    protected string $pathSeparator = '|||';

    // Код языка
    protected string $languageCode;

    // массив родительских и дочерних элементов, в порядке расположения в структуре переводов
    protected array $location;

    // Перевод (текст)
    protected $translation;

    // пути, для создания ключа кэширования
    protected string $languagePath = '';
    protected string $path = '';

    public function __construct(string $product, string $languageCode, array $location = [], $translation = '', ?string $prefix = null)
    {
        $this->product = $product;
        $this->languageCode = $languageCode;
        $this->location = $location;
        $this->translation = $translation;

        if ($prefix) {
            $this->prefix = $prefix;
        }
    }

    /**
     * Получение пути относительно родительских и дочерних элементов. Используется для кэширования
     *
     * @param string|null $separator
     * @return string
     */
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

    /**
     * Получение полного пути передода, с учетом языка и типа элемента.
     *
     * @param string|null $separator
     * @param array|null $location
     * @return string
     */
    private function getLanguagePath(?string $separator = null, ?array $location = null): string
    {
        if (empty($this->languagePath) || !is_null($separator)) {
            if (is_null($location)) {
                $location = $this->location ?: [];
            }

            $this->languagePath = implode(
                $separator ?: $this->pathSeparator,
                array_merge([$this->prefix, $this->product, $this->languageCode, $this->type], $location)
            );
        }

        return $this->languagePath;
    }

    /**
     * Формирование ключа для кэширования или поиска перевода в кэше
     *
     * @param CacheStorageTypes $storageType
     * @return string
     */
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

    /**
     * Получить имя родительского элемента
     *
     * @return string
     */
    public function getParentName(): string
    {
        if (count($this->location) === 0) {
            throw new RuntimeException('There is not parent level in this translation data.');
        }

        return reset($this->location);
    }

    /**
     * Получить имя элемента, к которому относится перевод
     * @return string
     */
    public function getItemName(): string
    {
        if (count($this->location) <= 1) {
            throw new RuntimeException('There is not item level in this translation data.');
        }

        return end($this->location);
    }

    /**
     * Установить новый перевод для элемента
     *
     * @param $translation
     * @return void
     */
    public function setTranslation($translation): void
    {
        $this->translation = $translation;
    }

    /**
     * Получить перевод или массив переводов для элемента
     *
     * @return mixed|string
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * Получить код языка
     *
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->languageCode;
    }

    /**
     * Получить имя продукта
     *
     * @return string
     */
    public function getProductName(): string
    {
        return $this->product;
    }
}