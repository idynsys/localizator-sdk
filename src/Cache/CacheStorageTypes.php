<?php

namespace Ids\Localizator\Cache;

use Ids\Localizator\Enum;

/**
 * Класс-enum для определения типов группировки переводов в кэш-хранилище
 */
final class CacheStorageTypes extends Enum
{
    // Хранить переводы отдельно для каждого элемента по языку.
    const TRANSLATIONS_STORAGE_TYPE = 'T';

    // Хранить переводы сгруппированными по родительским элементам в пределах одного языка
    const PARENTS_STORAGE_TYPE = 'P';

    // Хранить переводы всех элементов сгруппированные по языкам
    const LANGUAGE_STORAGE_TYPE = 'L';
}