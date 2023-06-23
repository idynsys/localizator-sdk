<?php

namespace Ids\Localizator\Cache;

use Ids\Localizator\DTO\TranslationData;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class TranslationCacheManager
{
    private const DEFAULT_EXPIRES_AFTER = '10 years';

    protected TranslationCacheTypes $storeType;
    protected CacheItemPoolInterface $cache;

    public function __construct(?CacheItemPoolInterface $cacheItemPool = null, ?TranslationCacheTypes $storeType = null)
    {
        $this->setCache($cacheItemPool);

        if (!$storeType) {
            $storeType = TranslationCacheTypes::TRANSLATIONS_STORE_TYPE();
        }

        $this->setStoreType($storeType);
    }

    public function setCache(?CacheItemPoolInterface $cacheItemPool = null): TranslationCacheManager
    {
        $this->cache = $cacheItemPool ?: new FilesystemAdapter();

        return $this;
    }

    public function setStoreType(TranslationCacheTypes $storeType): void
    {
        $this->storeType = $storeType;
    }

    public function clear(): void
    {
        $this->cache->clear();
    }

    public function get(TranslationData $translation): TranslationData
    {
        if ($this->cache->hasItem($translation->getKey())) {
            $translation->setTranslation($this->cache->getItem($translation->getKey())->get());
        }

        return $translation;
    }

    private function getExpAfter(): \DateInterval
    {
        return \DateInterval::createFromDateString(self::DEFAULT_EXPIRES_AFTER);
    }

    public function save(TranslationData $translation): void
    {
        switch ($this->storeType->getValue()) {
            case TranslationCacheTypes::PARENTS_STORE_TYPE:
                $key = $translation->getParentKey();
                break;
            case TranslationCacheTypes::LANGUAGE_STORE_TYPE:
                $key = $translation->getLanguageKey();
                break;
            default:
                $key = $translation->getkey();
                break;
        }

        $item = $this->cache->getItem($key);

        switch ($this->storeType->getValue()) {
            case TranslationCacheTypes::PARENTS_STORE_TYPE:
                $value = $this->cache->hasItem($key) ? $item->get() : [];

                $value[$translation->getItemName()] = $translation->getTranslation();
                break;
            case TranslationCacheTypes::LANGUAGE_STORE_TYPE:
                $value = $this->cache->hasItem($key) ? $item->get() : [];

                $value[$translation->getParentName()][$translation->getItemName()] = $translation->getTranslation();
                break;
            default:
                $value = $translation->getTranslation();
                break;
        }

        $item->set($value)->expiresAfter($this->getExpAfter());

        $this->cache->save($item);
    }
}