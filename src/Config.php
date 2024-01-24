<?php

namespace Ids\Localizator;

class Config
{
    private static $instance;

    private array $config;

    private function __construct()
    {
        $this->loadConfig();
    }

    private function loadConfig(): void
    {
        $this->config = require __DIR__ . '/Config/config.php';
    }

    private static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function get(string $key, $default = null)
    {
        $instance = self::getInstance();

        return array_key_exists($key, $instance->config) ? $instance->config[$key] : $default;
    }

    public static function set(string $key, $value): void
    {
        $instance = self::getInstance();

        $instance->config[$key] = $value;
    }

    public static function getHost(): ?string
    {
        return self::get(self::get('mode', 'DEVELOPMENT') === 'PRODUCTION' ? 'prod_host' : 'preprod_host');
    }
}