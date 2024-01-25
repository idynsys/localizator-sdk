<?php

namespace Idynsys\Localizator\DTO\Requests\Auth;

/**
 * Трейт для запросов с токенм аутентификации
 * @codeCoverageIgnore
 */
trait WithAuthorizationToken
{
    // Токен аутентификации
    private string $token;

    /**
     * Установить токен аутентификации
     *
     * @param $token
     * @return void
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    /**
     * Вернуть заголовки запроса с токеном аутентификации
     *
     * @return array
     */
    protected function getHeadersData(): array
    {
        return $this->addAuthToken(parent::getHeadersData());
    }

    /**
     * Добавить аутентификационный токен в заголовок запроса
     *
     * @param array $headerToken
     * @return array
     */
    protected function addAuthToken(array $headerToken): array
    {
        $headerToken['Authorization'] = 'Bearer ' . $this->token;

        return $headerToken;
    }
}