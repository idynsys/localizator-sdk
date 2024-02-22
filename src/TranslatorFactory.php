<?php

namespace Idynsys\Localizator;

use Idynsys\Localizator\Cache\TranslationCacheManager;
use Idynsys\Localizator\Client\Client;
use Idynsys\Localizator\Client\ClientBuilder;


class TranslatorFactory
{
    private Client $client;
    private TranslationCacheManager $cacheManager;

    public function __construct(?string $clientId = null, ?string $clientSecret = null
    ) {
        if ($clientId) {
            Config::set('clientId', $clientId);
        }

        if ($clientSecret) {
            Config::set('clientSecret', $clientSecret);
        }

        $this->configureDefaultCacheManager();
    }

    public static function create(?string $clientId = null, ?string $clientSecretKey = null): self {
        return new static($clientId, $clientSecretKey);
    }

    protected function configureDefaultCacheManager(): void
    {
        $this->setCacheManager(new TranslationCacheManager());
    }

    /**
     * @param TranslationCacheManager $cacheManager
     * @return TranslatorFactory
     */
    public function setCacheManager(TranslationCacheManager $cacheManager): TranslatorFactory
    {
        $this->cacheManager = $cacheManager;

        return $this;
    }

    /**
     * @param Client $client
     * @return TranslatorFactory
     */
    public function setClient(Client $client): TranslatorFactory
    {
        $this->client = $client;

        return $this;
    }

    public function build(): Translator
    {
        if (!isset($this->client)) {
            $this->client = ClientBuilder::create()->build();
        }

        if (!isset($this->cacheManager)) {
            $this->configureDefaultCacheManager();
        }

        return new Translator(
            $this->client,
            $this->cacheManager
        );
    }
}
