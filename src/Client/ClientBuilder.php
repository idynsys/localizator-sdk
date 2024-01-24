<?php

namespace Ids\Localizator\Client;

use GuzzleHttp\ClientInterface;
use Ids\Localizator\Config;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;

class ClientBuilder
{
    private ClientInterface $guzzleClient;
    private SerializerInterface $serializer;

    public function __construct()
    {
        $this->configureDefaultClient();
        $this->configureDefaultSerializer();
    }

    public static function create(): self
    {
        return new static();
    }

    /**
     * @param ClientInterface $guzzleClient
     * @return ClientBuilder
     */
    public function setGuzzleClient(ClientInterface $guzzleClient): ClientBuilder
    {
        $this->guzzleClient = $guzzleClient;

        return $this;
    }

    /**
     * @param SerializerInterface $serializer
     * @return ClientBuilder
     */
    public function setSerializer(SerializerInterface $serializer): ClientBuilder
    {
        $this->serializer = $serializer;

        return $this;
    }

    public function configureDefaultClient(): void
    {
        $this->guzzleClient = new \GuzzleHttp\Client(
            [
                'Content-Type' => 'application/json',
            ]
        );
    }

    public function configureDefaultSerializer(): void
    {
        $this->setSerializer(
            SerializerBuilder::create()
                //->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy()))
                ->build()
        );
    }

    public function build(): Client
    {
        return new Client($this->guzzleClient, $this->serializer);
    }
}
