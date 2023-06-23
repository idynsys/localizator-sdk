<?php

namespace Ids\Localizator\Cache;

use Ids\Localizator\Enum;

final class TranslationCacheTypes extends Enum
{
    const TRANSLATIONS_STORE_TYPE = 'T';
    const PARENTS_STORE_TYPE = 'P';
    const LANGUAGE_STORE_TYPE = 'L';
}