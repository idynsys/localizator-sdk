<?php

namespace Idynsys\Localizator\Client;

use Exception;
use GuzzleHttp\ClientInterface;
use Idynsys\Localizator\DTO\Requests\RequestData;
use Idynsys\Localizator\Exceptions\ExceptionHandler;
use Idynsys\Localizator\Exceptions\LocalizerSdkException;
use Idynsys\Localizator\Exceptions\RequestException;
use JMS\Serializer\SerializerInterface;
use Throwable;

class Client
{
    private ClientInterface $client;
    private SerializerInterface $serializer;

    // Exceptions возникший при выполнении запроса
    private ?Exception $error = null;

    private string $content = '';

    public function __construct(ClientInterface $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    /**
     * @param RequestData $data
     * @param bool $throwException
     * @return $this
     * @throws LocalizerSdkException
     */
    public function sendRequestToSystem(RequestData $data, bool $throwException = true): self
    {
        $this->error = null;

        try {
            $res = $this->client->request($data->getMethod(), $data->getUrl(), $data->getData());

            $this->content = $res->getBody()->getContents();
        } catch (Throwable $exception) {
            $handler = new ExceptionHandler($exception);
            $this->error = $handler->handle();
        }

        if ($this->error && $throwException) {
            throw $this->error;
        }

        return $this;
    }

    /**
     * Получить результат запроса. Если произошла ошибка, то вернется null
     *
     * @param string|null $key
     * @return string[]|null
     */
    public function getResult(?string $key = null): ?array
    {
        if (!isset($this->content) || $this->hasError()) {
            return null;
        }

        $data = $this->serializer->deserialize($this->content, 'array', 'json');

        if ($key && is_array($data)) {
            $data = [$key => array_key_exists($key, $data) ? $data[$key] : ''];
        }

        return $data;
    }

    /**
     * Проверить наличие ошибки в запросе
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->error !== null;
    }

    /**
     * Получить ошибку запроса, если она произошла
     *
     * @return array|null
     */
    public function getError(): ?array
    {
        if (!$this->hasError()) {
            return null;
        }

        if ($this->error instanceof RequestException) {
            return $this->error->getError();
        }

        return ['error' => $this->error->getMessage()];
    }
}
