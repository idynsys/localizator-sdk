<?php

namespace Idynsys\Localizator\DTO\Responses;

use Generator;
use Idynsys\Localizator\Collections\TranslationCollection;
use Idynsys\Localizator\Enums\TranslationTypes;

/**
 * Коллекция переводов статических элементов, полученная по запросу
 */
class StaticTranslationDataCollection
{
    // данные, полученные по запросу
    private array $originalTranslations;

    // Преобразованные данные в DTO-коллекцию
    private ?TranslationCollection $translationData = null;

    public function __construct(array $data)
    {
        $this->originalTranslations = $data['data'];
    }

    /**
     * Получить оригинальные данные, полученные по запросу
     *
     * @return array
     */
    public function getOriginalTranslations(): array
    {
        return $this->originalTranslations;
    }

    /**
     * Получить коллекцию DTO-переводов
     *
     * @param string|null $product - фильтр по продукту
     * @param string|null $language - фильтр по языку
     * @return TranslationCollection
     */
    public function getTranslations(?string $product = null, ?string $language = null): TranslationCollection
    {
        if ($product || $language) {
            return $this->filterTranslations($product, $language);
        }

        if ($this->translationData === null) {
            $this->translationData = new TranslationCollection();
            foreach ($this->translations() as $translation) {
                $this->translationData->addItem($translation);
            }
        }

        return $this->translationData;
    }

    /**
     * Функция, для работы с данными переводов через итератор
     *
     * @param string|null $searchProduct
     * @param string|null $searchLanguage
     * @return Generator
     */
    public function translations(?string $searchProduct = null, ?string $searchLanguage = null): Generator
    {
        foreach ($this->originalTranslations as &$product) {
            if ($searchProduct && $product['product_name'] !== $searchProduct) {
                continue;
            }
            foreach ($product['translations'] as $language => &$parents) {
                if ($searchLanguage && $language !== $searchLanguage) {
                    continue;
                }
                foreach ($parents as $parentName => &$children) {
                    foreach ($children as $childName => $translation) {
                        yield new StaticTranslationData(
                            $product['product_name'],
                            $language,
                            [$parentName, $childName],
                            $translation
                        );
                    }
                }
            }
        }
    }

    /**
     * Выборка переводов по параметрам поиска и возврат в виде коллекции DTO
     *
     * @param string|null $product
     * @param string|null $language
     * @return TranslationCollection
     */
    private function filterTranslations(?string $product, ?string $language): TranslationCollection
    {
        $result = new TranslationCollection();

        foreach ($this->translations($product, $language) as $translation) {
            $result->addItem($translation);
        }

        return $result;
    }

    public function toArray(): array
    {
        $translationArray = [];

        foreach ($this->getTranslations() as $translation) {
            $translationArray[] = [
                'productName' => $translation->getProductName(),
                'language' => $translation->getLanguage(),
                'parentName' => $translation->getParentName(),
                'itemName' => $translation->getItemName(),
                'translation' => $translation->getTranslation()
            ];
        }

        return $translationArray;
    }
}
