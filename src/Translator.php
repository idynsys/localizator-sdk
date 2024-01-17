<?php

namespace Ids\Localizator;

use GuzzleHttp\Exception\GuzzleException;
use Ids\Localizator\Cache\TranslationCacheManager;
use Ids\Localizator\Cache\CacheStorageTypes;
use Ids\Localizator\Client\Client;
use Ids\Localizator\Client\Request\StaticData\StaticTranslationRequest;
use Ids\Localizator\DTO\StaticTranslationData;
use Ids\Localizator\DTO\StaticTranslationDataCollection;
use Ids\Localizator\DTO\TranslationData;
use Psr\Cache\InvalidArgumentException;

class Translator
{
    private Client $client;
    private TranslationCacheManager $cacheManager;
    private string $applicationSecretKey;

    public function __construct(
        Client $client,
        TranslationCacheManager $cacheManager,
        string $applicationSecretKey
    ) {
        $this->client = $client;
        $this->cacheManager = $cacheManager;
        $this->applicationSecretKey = $applicationSecretKey;
    }

    /**
     * Изменить способ хранения переводов в кэше
     *
     * @param CacheStorageTypes $storageType
     * @return void
     */
    public function changeStorageType(CacheStorageTypes $storageType): void
    {
        $this->cacheManager->setStorageType($storageType);
    }

    /**
     * Получить перевод(ы) из кэша
     *
     * @param string $language
     * @param ...$location
     * @return TranslationData
     */
    public function getStaticItem(string $language, ...$location): TranslationData
    {
        return $this->cacheManager->get(new StaticTranslationData($language, $location));
    }

    /**
     * Получить переводы из локализатора
     *
     * @param string|null $languageCode
     * @return StaticTranslationDataCollection
     * @throws GuzzleException
     */
    public function importStaticItems(?string $languageCode = null): StaticTranslationDataCollection
    {
        return $this->client->getStaticTranslations(
            new StaticTranslationRequest($this->applicationSecretKey, $languageCode)
        );
    }

    /**
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function importStaticItemsInCache(?string $languageCode = null): void
    {
        $result = $this->importStaticItems($languageCode);

        foreach ($result->translations() as $translation) {
            $this->cacheManager->save($translation);
        }
    }

    /**
     * @return void
     */
    public function cacheClear(): void
    {
        $this->cacheManager->clear();
    }
}
