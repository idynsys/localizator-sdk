<?php

namespace Ids\Localizator\Cache;

use Ids\Localizator\Enum;

final class CacheStorageTypes extends Enum
{
    const TRANSLATIONS_STORAGE_TYPE = 'T';
    const PARENTS_STORAGE_TYPE = 'P';
    const LANGUAGE_STORAGE_TYPE = 'L';
}