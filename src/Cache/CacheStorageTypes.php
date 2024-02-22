<?php

namespace Idynsys\Localizator\Cache;

use Idynsys\Localizator\Enums\Enum;

/**
 * Класс-enum для определения типов группировки переводов в кэш-хранилище
 */
final class CacheStorageTypes extends Enum
{
    // Хранить переводы отдельно для каждого элемента по языку.
    public const TRANSLATIONS_STORAGE_TYPE = 'T';

    // Хранить переводы сгруппированными по родительским элементам в пределах одного языка
    public const PARENTS_STORAGE_TYPE = 'P';

    // Хранить переводы всех элементов сгруппированные по языкам
    public const LANGUAGE_STORAGE_TYPE = 'L';
}