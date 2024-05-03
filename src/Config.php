<?php

namespace Idynsys\Localizator;

use Idynsys\Localizator\Config\ConfigContract;

class Config implements ConfigContract
{
    private static ?Config $instance = null;

    private array $config;

    private function __construct()
    {
        $this->loadConfig();
    }

    private function loadConfig(): void
    {
        $this->config = require __DIR__ . '/Config/config.php';
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $instance = self::getInstance();

        return array_key_exists($key, $instance->config) ? $instance->config[$key] : $default;
    }

    public function set(string $key, mixed $value): void
    {
        $instance = self::getInstance();

        $instance->config[$key] = $value;
    }
}
