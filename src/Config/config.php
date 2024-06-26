<?php

return [
    // Идентификатор клиента
    'clientId'     => getenv('LOCALIZER_SDK_CLIENT_ID') ?: '',

    // Секретный ключ клиента
    'clientSecret' => getenv('LOCALIZER_SDK_APPLICATION_SECRET_KEY') ?: '',

    // Режим работы приложения с пакетом
    'mode' => getenv('LOCALIZER_SDK_MODE') ?: 'DEVELOPMENT',

    // продакшн хост
    'prod_host' => 'https://api-gateway.idynsys.org/api',

    // тестовый хост
    'preprod_host' => 'https://api-gateway.preprod.idynsys.org/api',

    // хост для разработки или динамо тестов
    'dev_host' => getenv('LOCALIZER_DEV_HOST') ?: 'https://api-gateway.preprod.idynsys.org/api',

    // url для получения токена аутентификации
    'AUTH_URL' => '/user-access/token',

    // url для получения статических переводов
    'STATIC_TRANSLATIONS_DATA_URL' => '/translations/for-application/static',

    // url для получения доступных языков приложения
    'APPLICATION_LANGUAGES_URL' => '/languages/for-application'
];
