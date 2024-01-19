<?php

namespace Ids\Localizator\Client;

use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Ids\Localizator\Client\Request\Catalogs\PostCatalogsItems\PostCatalogsItemsRequest;
use Ids\Localizator\Client\Request\StaticData\StaticTranslationRequest;
use Ids\Localizator\Client\Response\Catalogs\PostCatalogsItems\PostCatalogsItemsResult;
use Ids\Localizator\DTO\StaticTranslationDataCollection;
use Ids\Localizator\DTO\Requests\RequestData;
use JMS\Serializer\SerializerInterface;

class Client
{
    const URL_GET_STATIC_TRANSLATIONS = '/api/translations/for-application/static/{language}';

    private ClientInterface $client;
    private SerializerInterface $serializer;

    // Exception возникший при выполнении запроса
    private ?Exception $error = null;

    public function __construct(ClientInterface $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    private function getUrl(string $url, $attributes): string
    {
        foreach ($attributes as $key => $value) {
            $url = str_replace('{' . $key . '}', $value ?: '', $url);
        }

        return str_replace(array_keys($attributes), $attributes, $url);
    }

    /**
     * @throws GuzzleException
     */
    public function getStaticTranslations(StaticTranslationRequest $request): StaticTranslationDataCollection
    {
        $response = $this->client->get(
            $this->getUrl(self::URL_GET_STATIC_TRANSLATIONS, ['language' => $request->getLanguageCode()]),
            [
                RequestOptions::JSON => $request->getJsonParameters(),
                RequestOptions::HEADERS => $request->getHeaders()
            ]
        );

        $data = $this->serializer->deserialize(
            $response->getBody()->getContents(),
            'array',
            'json'
        );

        return new StaticTranslationDataCollection($data, $request->getLanguageCode());
    }

    /**
     * @throws GuzzleException
     */
    public function postCatalogItems(PostCatalogsItemsRequest $request): PostCatalogsItemsResult
    {
        $response = $this->client->post(
            '/api/localizer/catalogs/items',
            [
                RequestOptions::BODY => $this->serializer->serialize($request, 'json'),
            ]
        );

        $data = $this->serializer->deserialize(
            $response->getBody()->getContents(),
            'array<string,' . PostCatalogsItemsResult::class . '>',
            'json'
        );

        return $data['data'];
    }

    /**
     * @param RequestData $data
     * @param bool $throwException
     * @return $this
     */
    public function sendRequestToSystem(RequestData $data, bool $throwException = true): self
    {
        $this->error = null;

        try {
            dump($data->getMethod(), $data->getUrl(), $data->getData());
            $res = $this->client->request($data->getMethod(), $data->getUrl(), $data->getData());

            $this->content = $res->getBody()->getContents();
        } catch (\Throwable $exception) {
            dd($exception->getMessage(), $exception);
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
        if ($this->hasError() || !isset($this->content)) {
            return null;
        }

        $data = json_decode($this->content, true, 512, JSON_THROW_ON_ERROR);

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
        return !is_null($this->error);
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

        return $this->error->getError();
    }

}
