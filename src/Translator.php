<?php

namespace Ids\Localizator;

use DateTimeInterface;
use GuzzleHttp\Exception\GuzzleException;
use Ids\Localizator\Client\Client;
use Ids\Localizator\Client\Request\Catalogs\PostCatalogsItems\PostCatalogsItemsRequest;
use Ids\Localizator\Client\Request\Catalogs\PostCatalogsItems\Translation;
use Ids\Localizator\Client\Request\StaticData\StaticTranslationRequest;
use Ids\Localizator\DTO\StaticTranslationData;
use Ids\Localizator\DTO\StaticTranslationDataCollection;
use Ids\Localizator\DTO\TranslationData;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class Translator
{
    private const DEFAULT_EXPIRES_AFTER = '10 years';
    private const LAST_WARMING_TIME_KEY = 'translator_last_warming_time';
    public const PARENT_TYPE_CATALOG = 'C';
    public const PARENT_TYPE_UI_ITEM = 'I';
    private bool $warmCacheIfEmpty = false;

    private Client $client;
    private CacheItemPoolInterface $itemPool;
    private string $applicationSecretKey;
    private string $applicationName = 'no-app';
    private ?string $currentLang;
    private ?int $organizationId;

    public function __construct(
        Client $client,
        CacheItemPoolInterface $itemPool,
        string $applicationSecretKey,
        ?string $currentLang = null,
        ?int $organizationId = null
    ) {
        $this->client = $client;
        $this->itemPool = $itemPool;
        $this->applicationSecretKey = $applicationSecretKey;
        $this->currentLang = $currentLang;
        $this->organizationId = $organizationId;
    }

    private function getCacheKey(
        string $type,
        string $lang,
        string $categoryName,
        string $code
    ): string {
        return sprintf(
            '%s:%s-%s_%s-%s',
            $type,
            $this->applicationName,
            strtolower($lang),
            $categoryName,
            $code
        );
    }

    private function getExpAfter(): \DateInterval
    {
        return \DateInterval::createFromDateString(self::DEFAULT_EXPIRES_AFTER);
    }

    /**
     * @param bool $warmCacheIfEmpty
     * @return Translator
     */
    public function setWarmCacheIfEmpty(bool $warmCacheIfEmpty): Translator
    {
        $this->warmCacheIfEmpty = $warmCacheIfEmpty;

        return $this;
    }

    public function getStaticTranslation(StaticTranslationData $translationData)
    {

    }

    /**
     * @deprecated
     *
     * @throws InvalidArgumentException
     * @throws GuzzleException
     */
    private function getTranslationByType(
        string $type,
        string $catalogName,
        string $code
    ) {
        if ($this->warmCacheIfEmpty && $this->getLatestWarming() === null) {
            $this->warmCache();
        }

        $key = $this->getCacheKey(
            $type,
            $this->currentLang,
            $catalogName,
            $code
        );

        if ($this->itemPool->hasItem($key)) {
            return $this->itemPool->getItem($key)->get();
        }

        return null;
    }

    /**
     * @param string|null $languageCode
     * @return StaticTranslationDataCollection
     * @throws GuzzleException
     */
    public function importStaticTranslations(?string $languageCode = null): StaticTranslationDataCollection
    {
        $data = $this->client->getStaticTranslations(
            new StaticTranslationRequest($this->applicationSecretKey, $languageCode)
        );

        return $data;
    }

    /**
     * @deprecated
     *
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function translateUi(string $catalogName, string $code, int $productId = null): ?TranslationString
    {
        $translation = $this->getTranslationByType(self::PARENT_TYPE_UI_ITEM, $catalogName, $code, $productId);
        return $translation ? new TranslationString($translation) : null;
    }

    /**
     * @throws GuzzleException
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function addTranslation(
        string $catalogName,
        string $code,
        string $value,
        string $type = 'I'
    ): void {
        $postRequest = new PostCatalogsItemsRequest(
            $this->applicationSecretKey,
            $catalogName,
            $code,
            null,
            [
                new Translation($this->currentLang, $value),
            ],
            $this->organizationId
        );

        $result = $this->client->postCatalogItems($postRequest);
        foreach ($result->getTranslations() as $translation) {
            $this->saveItem(
                $translation->getLanguageCode(),
                $catalogName,
                $result->getItemId(),
                $translation->getTranslation(),
                $type
            );
        }
    }

    /**
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function reset(): void
    {
        $this->itemPool->clear();
        $this->warmCache();
    }

    /**
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    private function warmCache(): void
    {
        $result = $this->client->getStaticTranslations(
            new StaticTranslationRequest($this->applicationSecretKey)
        );

        foreach ($result->translations() as $translation) {
            $this->saveItem($translation);
        }

        $lastWarmingTimeItem = $this->itemPool->getItem(self::LAST_WARMING_TIME_KEY);
        $lastWarmingTimeItem->set((new \DateTime())->format(DateTimeInterface::ATOM));
        $this->itemPool->save($lastWarmingTimeItem);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getLatestWarming(): ?\DateTime
    {
        if ($this->itemPool->hasItem(self::LAST_WARMING_TIME_KEY)) {
            $lastWarmingTimeItem = $this->itemPool->getItem(self::LAST_WARMING_TIME_KEY);

            return \DateTime::createFromFormat(DateTimeInterface::ATOM, $lastWarmingTimeItem->get());
        }

        return null;
    }

}
