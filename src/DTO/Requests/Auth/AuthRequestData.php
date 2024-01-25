<?php

namespace Idynsys\Localizator\DTO\Requests\Auth;

use Idynsys\Localizator\Config;
use Idynsys\Localizator\DTO\Requests\RequestData;
use Idynsys\Localizator\Enums\RequestMethod;

/**
 * Класс DTO для запроса на получение токена аутентификации
 */
final class AuthRequestData extends RequestData
{
    // Код конфигурации, на урл запроса
    protected string $urlConfigKeyForRequest = 'AUTH_URL';

    // Метод запроса
    protected string $requestMethod = RequestMethod::METHOD_POST;

    /**
     * Получить данные для запроса
     *
     * @return array{clientId: string}
     */
    protected function getRequestData(): array
    {
        return [
            'clientId' => Config::get('clientId'),
        ];
    }
}