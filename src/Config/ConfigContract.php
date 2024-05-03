<?php

namespace Idynsys\Localizator\Config;

interface ConfigContract
{
    public function set(string $key, mixed $value): void;

    public function get(string $key, mixed $default = null): mixed;
}
