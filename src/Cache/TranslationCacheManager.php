<?php

namespace Ids\Localizator\Cache;

use DateInterval;
use Ids\Localizator\DTO\TranslationData;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class TranslationCacheManager
{
    private const DEFAULT_EXPIRES_AFTER = '10 years';

    protected CacheStorageTypes $storageType;
    protected CacheItemPoolInterface $cache;

    public function __construct(?CacheItemPoolInterface $cacheItemPool = null, ?CacheStorageTypes $storageType = null)
    {
        $this->setCache($cacheItemPool);

        if (!$storageType) {
            $storageType = CacheStorageTypes::TRANSLATIONS_STORAGE_TYPE();
        }

        $this->setStorageType($storageType);
    }

    /**
     * @param CacheItemPoolInterface|null $cacheItemPool
     * @return $this
     */
    public function setCache(?CacheItemPoolInterface $cacheItemPool = null): TranslationCacheManager
    {
        $this->cache = $cacheItemPool ?: new FilesystemAdapter();

        return $this;
    }

    /**
     * @param CacheStorageTypes $storageType
     * @return void
     */
    public function setStorageType(CacheStorageTypes $storageType): void
    {
        $this->storageType = $storageType;
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->cache->clear();
    }

    /**
     * @param TranslationData $translation
     * @return TranslationData
     * @throws InvalidArgumentException
     */
    public function get(TranslationData $translation): TranslationData
    {
        if ($this->cache->hasItem($translation->getKey($this->storageType))) {
            $translation->setTranslation($this->cache->getItem($translation->getKey($this->storageType))->get());
        }

        return $translation;
    }

    /**
     * @return DateInterval
     */
    private function getExpirationDate(): DateInterval
    {
        return DateInterval::createFromDateString(self::DEFAULT_EXPIRES_AFTER);
    }

    /**
     * @param TranslationData $translation
     * @return void
     * @throws InvalidArgumentException
     */
    public function save(TranslationData $translation): void
    {
        $key = $translation->getKey($this->storageType);
        $item = $this->cache->getItem($key);

        switch ($this->storageType->getValue()) {
            case CacheStorageTypes::PARENTS_STORAGE_TYPE:
                $value = $this->cache->hasItem($key) ? $item->get() : [];

                $value[$translation->getItemName()] = $translation->getTranslation();
                break;
            case CacheStorageTypes::LANGUAGE_STORAGE_TYPE:
                $value = $this->cache->hasItem($key) ? $item->get() : [];

                $value[$translation->getParentName()][$translation->getItemName()] = $translation->getTranslation();
                break;
            default:
                $value = $translation->getTranslation();
                break;
        }

        $item->set($value)->expiresAfter($this->getExpirationDate());

        $this->cache->save($item);
    }
}