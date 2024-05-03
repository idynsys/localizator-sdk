<?php

namespace Idynsys\Localizator\DTO\Requests;

use Idynsys\Localizator\Config;
use Idynsys\Localizator\Config\ConfigContract;
use Idynsys\Localizator\Enums\RequestMethod;
use Idynsys\Localizator\Enums\SdkMode;

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

    protected ConfigContract $config;

    protected string $clientId = '';

    protected string $clientSecret = '';

    public function __construct(string $requestMethod, string $urlConfigKeyForRequest, ?ConfigContract $config = null)
    {
        $this->config = $config ?: Config::getInstance();
        $this->clientId = $this->config->get('clientId');
        $this->clientSecret = $this->config->get('clientSecret');
        $this->requestMethod = $requestMethod;
        $this->urlConfigKeyForRequest = $urlConfigKeyForRequest;
    }

    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * Получить url хоста, по которому будет выполняться запрос
     *
     * @param string $mode
     * @return string
     */
    protected function getHostByMode(string $mode): string
    {
        switch ($mode) {
            case SdkMode::PRODUCTION:
                return $this->config->get('prod_host');
            case SdkMode::PREPROD:
                return $this->config->get('preprod_host');
            default:
                return $this->config->get('dev_host');
        }
    }

    /**
     * Получить полный URL для выполнения запроса с учетом режима работы приложения
     *
     * @return string
     */
    protected function getRequestUrlConfigKey(): string
    {
        $mode = $this->config->get('mode', SdkMode::DEVELOPMENT);
        $host = $this->getHostByMode($mode);

        return $host . $this->config->get($this->urlConfigKeyForRequest);
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

    protected function getSignature(): string
    {
        $dataForSign = $this->getRequestData();

        if ($this->getMethod() === RequestMethod::METHOD_GET) {
            array_walk_recursive($dataForSign, function (&$item) {
                if (is_numeric($item)) {
                    $item = (string)$item;
                }
            });
        }

        return hash_hmac(
            'sha512',
            json_encode($dataForSign, JSON_THROW_ON_ERROR),
            $this->clientSecret
        );
    }

    /**
     * Получить данные заголовка
     *
     * @return array{X-Authorization-Sign: string}
     */
    protected function getHeadersData(): array
    {
        return [
            'X-Client-Id' => $this->clientId,
            'X-Authorization-Sign' => $this->getSignature()
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
