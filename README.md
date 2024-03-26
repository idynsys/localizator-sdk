# Localizator SDK

## Описание

Пакет предназначен для интеграции внешней системы на PHP 7.4+ и B2B backoffice
сервиса Localizer, для того, чтобы получать данные по переводам элементов, 
зарегистрированным в B2B backoffice сервиса Localizer.

При помощи данного пакета можно:

- Получить переводы статических элементов для приложения по языку 
или по всем языкам сразу, на которые настроено ваше приложение 
в "B2B Backoffice".
- Настроить тип кэширования: по отдельным ключам, по родительским 
элементам, по языку.
- Использовать свою систему кэширования или по умолчанию.
- Загрузить все переводы для статических элементов в кэш из B2B backoffice сервиса Localizer.
- Извлечь переводы из кэша.
- Очистить кэш.

## Установка
1. В каталоге Вашего проекта, где расположен файл composer.json, выполните команду:
```shell script
composer req idynsys/localizator
```
2. Настройка Вашего приложения для выполнения запроса к B2B Backoffice.<br>  
   Для выполнения запроса необходимо в запросах передавать информацию об идентификаторе
   приложения с использованием секретного ключа для подписи параметров запрос. Это
   можно сделать двумя способами.<br>  
   2.1. Через переменные окружения:<br>  
   В переменных окружения приложения, где устанавливается этот пакет, необходимо создать
   переменные окружения:
    ```dotenv
    LOCALIZER_SDK_CLIENT_ID=<clientId>
    LOCALIZER_SDK_APPLICATION_SECRET_KEY=<secret>
    ```
   <br>  

   2.2. Через создание объекта от класса Translator:
   ```php
   use Idynsys\Localizator\TranslatorFactory;
   use Idynsys\Localizator\Translator;
   
   /** @var Translator $translator */
   $translator = TranslatorFactory::create('<clientId>', '<secret>')->build();
   ```

   где "clientId" и "secret" будут переданы Вашей компании после регистрации внешнего
   приложения в B2B Backoffice для возможности выполнения запросов через B2B.

<br>
3. !!! Для версии на Production необходимо установить переменную окружения:

```dotenv
LOCALIZER_SDK_MODE=PRODUCTION
```
Если эта переменная не установлена или имеет другое значение, то все запросы
будут перенаправляться на тестовый сервер B2B Backoffice.

## Использование

### Создать экземпляр класса Translator:
```php
<?php

use Idynsys\Localizator\TranslatorFactory;
use Idynsys\Localizator\Translator;

// Если "clientId" и "secret" установлены через переменные окружения (см. п.2.1.)
/** @var Translator $translator */
$translator = TranslatorFactory::create()->build();
...

// или через прямое указание через параметры (см. п.2.2.)   

/** @var Translator $translator */
$translator = TranslatorFactory::create('<clientId>', '<secret>')->build();
...
```

### Получить переводы для статических элементов из Локализатора: ###

```php
    // переводы для всех языков
    $allTranslations = $translator->getStaticItems();
    
    // переводы для определенного языка приложения
    $rusTranslations = $translator->getStaticItems('rus');
```
Загружаться будут только те элементы, для которых определены переводы. 
При отсутствии перевода для одного или нескольких языков, элемент не будет 
включен в результат.

Результатом загрузки будет объект класса
` Idynsys\Localizator\DTO\StaticTranslationDataCollection `

Получить из объекта $allTranslations или $rusTranslations данные можно следующими
способами:

1. Оригинальные данные в виде многомерного массива, с сохранением иерархии для
родительских и дочерних записей в пределах каждого продукта и языка:

```php
    var_dump($allTranslations->getOriginalTranslations();
```
результат:
```php
array:1 [
  "data" => array:2 [
    0 => array:3 [
      "product_id" => 10
      "product_name" => "Test product name"
      "translations" => array:3 [
        "eng" => array:1 [
          "Test form" => array:2 [
            "Title1" => "Test form title1"
            "Title2" => "Test form title2"
          ]
        ]
        "rus" => array:1 [
        "Test form" => array:2 [
          "Title1" => "Название 1 тестовой формы"
          "Title2" => "Название 2 тестовой формы"
        ]
      ]
    ]
```
2. Через коллекцию объектов \Idynsys\Localizator\DTO\StaticTranslationData:<br>
   
2.1. Все переводы:
```php
use Idynsys\Localizator\DTO\StaticTranslationData;

/** @var StaticTranslationData $translation */
foreach ($allTranslations->translations() as $translation) {
    var_dump($translation->getTranslation());
}
```

2.2. Все переводы для определенного продукта:
```php
use Idynsys\Localizator\DTO\StaticTranslationData;

/** @var StaticTranslationData $translation */
foreach ($allTranslations->translations('Test product name') as $translation) {
    var_dump($translation->getTranslation());
}
```

2.2. Все переводы для определенного продукта для английского языка:
```php
use Idynsys\Localizator\DTO\StaticTranslationData;

/** @var StaticTranslationData $translation */
foreach ($allTranslations->translations('Test product name', 'rus') as $translation) {
    var_dump($translation->getTranslation());
}
```

2.3. Все переводы для английского языка:
```php
use Idynsys\Localizator\DTO\StaticTranslationData;

/** @var StaticTranslationData $translation */
foreach ($allTranslations->translations(null, 'rus') as $translation) {
    var_dump($translation->getTranslation());
}
```

-------------------------------------

### Загрузить переводы в кэш ###

1. Загрузить переводы для статических элементов для всех языков
```php
    $translator->setStaticItemsToCache()
```

2. Загрузить переводы для статических элементов для определенного языка
```php
    $translator->setStaticItemsToCache('eng')
```
-------------------------------------

### Настройка типа кэширования ###

По умолчанию, переводы будут сохраняться отдельно для каждого элемента,
полученного из Локализатора. Но при необходимости, это можно изменить, 
для хранения переводов по группам или по языкам. Это будет удобно, 
когда вы захотите получить все переводы для определенной формы, модуля
или группы сообщений об ошибках.

Предусмотрены 3 типа:
```php
    // (по умолчанию) Сохраняется каждый перевод отдельно в кэш под своими ключами
    $cacheStorageType = CacheStorageTypes::TRANSLATIONS_STORAGE_TYPE();
    
    // Переводы сохраняются с группировкой по родительскому элементу
    $cacheStorageType = CacheStorageTypes::PARENTS_STORAGE_TYPE();
    
    // Переводы сохраняются с группировкой по коду языка
    $cacheStorageType = CacheStorageTypes::LANGUAGE_STORAGE_TYPE();
```

Чтобы изменить способ формирования ключа для кэширования, необходимо 
вызывать функцию:
```php
    $translator->changeStorageType($cacheStorageType);
```

Переключать тип вы можете без ограничений, в процессе работы с переводами,
сохранив переводы в каждом из 3-х вариантов и потом обращаться к ним,
при необходимости, на том уровне, который будет более оптимален в каждом
конкретном случае. 

-------------------------------------

### Извлечь переводы из кэша ###

1. При типе кэширования ` TRANSLATIONS_STORAGE_TYPE `:
```php
    /** @var  \Idynsys\Localizator\DTO\StaticTranslationData $cashedData */
    $cachedData = $translator->getStaticItemFromCache('Test product name', 'eng', 'Test form', 'Title');
    
    echo $cachedData->getTranslation();
```
результат:
```
    "Test form title" 
```

2. При типе кэширования ` PARENTS_STORAGE_TYPE `:
```php
    /** @var  \Idynsys\Localizator\DTO\StaticTranslationData $cashedData */
    $cachedData = $translator->getStaticItem('Test product name', 'rus');

    var_dump($cachedData->getTranslation()); 
```
результат:
```php
    array:1 [
      "Title" => "Test form title"
    ]
```

3. При типе кэширования ` LANGUAGE_STORAGE_TYPE `:
```php
    /** @var  \Idynsys\Localizator\DTO\StaticTranslationData $cashedData */
    $cachedData = $translator->getStaticItem('eng');

    var_dump($cachedData->getTranslation()); 
```
результат:
```php
    array:1 [
      "Test form" => array:1 [
        "Title" => "Test form title"
      ]
    ]
```
------------------------------------
### Очистить кэш ###

```php
$translator->cacheClear();
```

-------------------------------------

Разное

```php
//использовать собственную реализацию кешера Psr\Cache\CacheItemPoolInterface

$translator = TranslatorFactory::create($applicationId, 'rus')
                ->setCache(new RedisAdapter(new \Redis()))
                ->build();

```


Некоторые команды для разработки

```php
docker-compose run --rm php-cli composer --version
docker-compose run --rm php-cli composer install
```

### Code Style
### Доступные команды
- composer cs - проверка код стандарта
- composer cs-diff - проверка код стандарта в текущей GIT ветке
- composer cs-fix - автоматическое исправление кода под стандарт
- composer stan - базовый статический анализ кода всей библиотеки (PHPStan)
- composer stan-diff - статический анализ обновленного кода в текущей GIT ветке (PHPStan)
