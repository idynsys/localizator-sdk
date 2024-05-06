<?php

namespace Idynsys\Localizator\DTO\Requests\Translations;

use Idynsys\Localizator\Config\ConfigContract;
use Idynsys\Localizator\DTO\Requests\RequestData;
use Idynsys\Localizator\Enums\RequestMethod;

/**
 * Данные для запроса на получение переводов для статических элементов
 */
class StaticTranslationsRequestData extends RequestData
{
    // Код языка для получения переводов
    protected ?string $languageCode;

    public function __construct(?string $languageCode = null, ?ConfigContract $config = null)
    {
        parent::__construct(RequestMethod::METHOD_GET, 'STATIC_TRANSLATIONS_DATA_URL', $config);

        $this->languageCode = $languageCode;
    }

    /**
     * Модификация URL запроса
     *
     * @return string
     */
    public function getUrl(): string
    {
        return parent::getUrl() . ($this->languageCode ? '/' . $this->languageCode : '');
    }
}
