<?php

namespace Ids\Localizator;

use Ids\Localizator\Client\Client;
use Ids\Localizator\Client\ClientBuilder;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;


class TranslatorFactory
{
    private Client $client;
    private CacheItemPoolInterface $cache;
    private string $applicationSecretKey;
    private ?string $currentLang;
    private ?string $localizatorUrl;

    public function __construct(
        string $applicationSecretKey,
        ?string $currentLang = null
    ) {
        $this->applicationSecretKey = $applicationSecretKey;
        $this->currentLang = $currentLang;
        $this->configureDefaultCacheAdapter();
    }

    public static function create(
        string $applicationSecretKey,
        ?string $currentLang = null,
        int $organizationId = null
    ): self {
        return new static($applicationSecretKey, $currentLang, $organizationId);
    }

    public function configureDefaultCacheAdapter(): void
    {
        $this->cache = new FilesystemAdapter();
    }

    /**
     * @param CacheItemPoolInterface $cacheItemPool
     * @return TranslatorFactory
     */
    public function setCache(CacheItemPoolInterface $cacheItemPool): TranslatorFactory
    {
        $this->cache = $cacheItemPool;

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

        if (!isset($this->cache)) {
            $this->configureDefaultCacheAdapter();
        }

        $translator = new Translator(
            $this->client,
            $this->cache,
            $this->applicationSecretKey,
            $this->currentLang
        );

        $translator->setWarmCacheIfEmpty(true);

        return $translator;
    }
}
