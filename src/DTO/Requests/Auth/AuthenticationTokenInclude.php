<?php

namespace Ids\Localizator\DTO\Requests\Auth;

/**
 * Интерфейс для запросов с токеном аутентификации
 */
interface AuthenticationTokenInclude
{
    /**
     * Установить аутентификационный токен
     *
     * @param $token
     * @return void
     */
    public function setToken($token): void;
}