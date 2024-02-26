<?php

namespace Idynsys\Localizator\Enums;

use InvalidArgumentException;

class Enum
{
    protected string $_value;

    protected function __construct(string $value) {
        $this->_value = $value;
    }

    public function is($key): bool
    {
        return $this->_value === $key;
    }

    public function getValue(): string
    {
        return $this->_value;
    }

    public static function __callStatic($name, $params) {
        $value = constant("static::$name");
        if (!$value) {
            throw new InvalidArgumentException(static::class . " can't be $name");
        }
        return new static($value);
    }

    public function __toString() {
        return $this->_value;
    }
}