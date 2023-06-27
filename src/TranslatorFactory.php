<?php

namespace Ids\Localizator;

use Ids\Localizator\Cache\TranslationCacheManager;
use Ids\Localizator\Client\Client;
use Ids\Localizator\Client\ClientBuilder;


class TranslatorFactory
{
    private Client $client;
    private TranslationCacheManager $cacheManager;
    private string $applicationSecretKey;
    private ?string $localizatorUrl;

    public function __construct(
        string $applicationSecretKey
    ) {
        $this->applicationSecretKey = $applicationSecretKey;
        $this->configureDefaultCacheManager();
    }

    public static function create(string $applicationSecretKey): self {
        return new static($applicationSecretKey);
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

    /**
     * @param string|null $localizatorUrl
     * @return TranslatorFactory
     */
    public function setLocalizatorUrl(?string $localizatorUrl): TranslatorFactory
    {
        $this->localizatorUrl = $localizatorUrl;
        return $this;
    }

    public function build(): Translator
    {
        if (!isset($this->client)) {
            $this->client = ClientBuilder::create($this->localizatorUrl)->build();
        }

        if (!isset($this->cacheManager)) {
            $this->configureDefaultCacheManager();
        }

        $translator = new Translator(
            $this->client,
            $this->cacheManager,
            $this->applicationSecretKey
        );

        return $translator;
    }
}
