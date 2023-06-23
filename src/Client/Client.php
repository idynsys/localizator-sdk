<?php

namespace Ids\Localizator\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Ids\Localizator\Client\Request\Catalogs\PostCatalogsItems\PostCatalogsItemsRequest;
use Ids\Localizator\Client\Request\StaticData\StaticTranslationRequest;
use Ids\Localizator\Client\Response\Catalogs\PostCatalogsItems\PostCatalogsItemsResult;
use Ids\Localizator\DTO\StaticTranslationDataCollection;
use JMS\Serializer\SerializerInterface;

class Client
{
    const URL_GET_STATIC_TRANSLATIONS = '/api/translations/for-application/static/{language}';

    private ClientInterface $client;
    private SerializerInterface $serializer;

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
}
