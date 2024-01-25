<?php

namespace Ids\Localizator\DTO\Requests;

use Ids\Localizator\Config;
use Ids\Localizator\Enums\RequestMethod;

/**
 * DTO для запроса. От этого класса наследуются все DTO для запросов
 * @codeCoverageIgnore
 */
abstract class RequestData
{
    // метод запроса
    protected string $requestMethod;

    // URL из конфигурации для выполнения запрос, заполняется в конкретном классе-наследнике
    protected string $urlConfigKeyForRequest;

    /**
     * Получить полный URL для выполнения запроса с учетом режима работы приложения
     *
     * @return string
     */
    protected function getRequestUrlConfigKey(): string
    {
        return Config::getHost() . Config::get($this->urlConfigKeyForRequest);
    }

    /**
     * Получить API url для выполнения запроса
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->getRequestUrlConfigKey();
    }

    /**
     * Получить данные, отправляемые в запросе
     * @return array
     */
    protected function getRequestData(): array
    {
        return [];
    }

    /**
     * Подучить метод запроса
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->requestMethod ?: RequestMethod::METHOD_GET;
    }

    /**
     * Получить данные и заголовки передаваемые в запрос
     *
     * @return array
     */
    public function getData(): array
    {
        $paramsType = $this->getMethod() === RequestMethod::METHOD_POST ? 'json' : 'query';

        return [
            'headers' => $this->getHeadersData(),
            $paramsType => $this->getRequestData()
        ];
    }

    /**
     * Получить данные заголовка
     *
     * @return array{X-Authorization-Sign: string}
     */
    protected function getHeadersData(): array
    {
        return [
            'X-Authorization-Sign' => hash_hmac(
                'sha512',
                json_encode($this->requestMethod === RequestMethod::METHOD_GET ? [] : $this->getRequestData()),
                Config::get('clientSecret')
            )
        ];
    }

    /**
     * Преобразование float в строку с двумя знаками после запятой
     *
     * @param float $number
     * @return string
     */
    protected function roundAmount(float $number): string
    {
        return number_format((float) $number, 2, '.', '');
    }
}