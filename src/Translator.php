<?php

namespace Idynsys\Localizator;

use GuzzleHttp\Exception\GuzzleException;
use Idynsys\Localizator\Cache\TranslationCacheManager;
use Idynsys\Localizator\Cache\CacheStorageTypes;
use Idynsys\Localizator\Client\Client;
use Idynsys\Localizator\DTO\Requests\RequestData;
use Idynsys\Localizator\DTO\Requests\Translations\StaticTranslationsRequestData;
use Idynsys\Localizator\DTO\StaticTranslationData;
use Idynsys\Localizator\DTO\StaticTranslationDataCollection;
use Idynsys\Localizator\DTO\TranslationData;
use Psr\Cache\InvalidArgumentException;

class Translator
{
    private Client $client;

    private TranslationCacheManager $cacheManager;

    // Количество попыток для запроса токена аутентификации
    private int $requestAttempts = 3;

    // Сохраняет токен для выполнения операций по счету
    private ?string $token = null;

    public function __construct(
        Client $client,
        TranslationCacheManager $cacheManager
    ) {
        $this->client = $client;
        $this->cacheManager = $cacheManager;
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
    public function getStaticItemFromCache(string $product, string $language, ...$location): TranslationData
    {
        return $this->cacheManager->get(new StaticTranslationData($product, $language, $location));
    }

    /**
     *  Отправить запрос в B2B Backoffice
     *
     * @param RequestData $data
     * @return void
     */
    private function sendRequest(RequestData $data): void
    {
        $this->client->sendRequestToSystem($data);
    }

    /**
     * Получить переводы из Локализатора
     *
     * @param string|null $languageCode
     * @return StaticTranslationDataCollection
     * @throws GuzzleException
     */
    public function getStaticItems(?string $languageCode = null): StaticTranslationDataCollection
    {
        $data = new StaticTranslationsRequestData($languageCode);
        $this->sendRequest($data);

        $result = $this->client->getResult();

        return new StaticTranslationDataCollection($result);
    }

    /**
     * Загрузить данные переводов в кэш
     *
     * @param string|null $languageCode
     * @return void
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function setStaticItemsToCache(?string $languageCode = null): void
    {
        $result = $this->getStaticItems($languageCode);

        foreach ($result->translations() as $translation) {
            $this->cacheManager->save($translation);
        }
    }

    /**
     * Очистить кэш
     *
     * @return void
     */
    public function cacheClear(): void
    {
        $this->cacheManager->clear();
    }
}
