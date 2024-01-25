<?php

namespace Ids\Localizator\DTO\Requests\Translations;

use Ids\Localizator\DTO\Requests\Auth\AuthenticationTokenInclude;
use Ids\Localizator\DTO\Requests\RequestData;
use Ids\Localizator\DTO\Requests\Auth\WithAuthorizationToken;
use Ids\Localizator\Enums\RequestMethod;

/**
 * Данные для запроса на получение переводов для статических элементов
 */
class StaticTranslationsRequestData extends RequestData implements AuthenticationTokenInclude
{
    use WithAuthorizationToken;

    // Метод запроса
    protected string $requestMethod = RequestMethod::METHOD_GET;

    // URL из конфигурации для выполнения запроса
    protected string $urlConfigKeyForRequest = 'STATIC_TRANSLATIONS_DATA_URL';

    // Код языка для получения переводов
    protected ?string $languageCode;

    public function __construct(?string $languageCode = null)
    {
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