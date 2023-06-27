# IDS api client sdk

## Описание

Пакет предназначен для получения переводов из локализатора. Для того,
чтобы получить данные из локализатора, необходимо знать 
Application Secret Key для своего приложения, зарегистрированного 
в "B2B Backoffice".

При помощи данного пакета можно:

- Получить переводы для статических элементов для приложения по языку 
или по всем языкам сразу, на которые настроено ваше приложение 
в "B2B Backoffice".
- Настроить тип кэширования: по отдельным ключам, по родительским 
элементам, по языку.
- Использовать свою систему кэширования или по умолчанию.
- Загрузить все переводы для статических элементов в кэш из локализатора.
- Извлечь переводы из кэша.
- Очистить кэш.

## Установка

```shell script
composer req idynsys/localizator
```

## Как использовать

***Создать экземпляр переводчика***

```
$translator = TranslatorFactory::create('Application Secret Key')->build();
```
В качестве параметров передать символьное значение секретного ключа вашего приложения.

--------------------------------

***Получить переводы для статических элементов из локализатора:***

```
    // переводы для всех языков
    $allTranslations = $translator->importStaticItems();
    
    // переводы для определенного языка приложения
    $rusTranslations = $translator->importStaticItems('rus');
```
Загружаться будут только те элементы, для которых определены переводы. При отсутствии перевода для одного или нескольких языков, элемент не будет включен в результат.

Результатом загрузки будет объект класса
` Ids\Localizator\DTO\StaticTranslationDataCollection `

Получить из объекта этого класса данные можно двумя способами:

1. В виде многомерного массива, с сохранением иерархии для родительских
и дочерних записей в пределах каждого языка:


   1.1. все значения для всех языков
```
    var_dump($allTranslations->getTranslations();
```
результат:
```
    array:2 [
      "eng" => array:1 [
        "Test form" => array:1 [
          "Title" => "Test form title"
        ]
      ]
      "rus" => array:1 [
        "Test form" => array:1 [
          "Title" => "Название тестовой формы"
        ]
      ]
    ]
```
   1.2. значения для конкретного языка
```
    var_dump($allTranslations->getTranslations('rus');
```
результат:
```
    array:1 [
      "Test form" => array:1 [
        "Title" => "Название тестовой формы"
      ]
    ]
```
2. Через итератор:
```
    foreach ($allTranslations->translations() as $translation) {
        $translation ...
        ...
    }
```
Элементом итератора является объект класса ` StaticTranslationData `

-------------------------------------

***Загрузить переводы в кэш***

Загрузить переводы для статических элементов для всех языков
```
    $translator->importStaticItemsInCache()
```

Загрузить переводы для статических элементов для определенного языка
```
    $translator->importStaticItemsInCache('eng')
```
-------------------------------------

***Настройка типа кэширования***

По умолчанию, переводы будут сохраняться отдельно для каждого элемента,
полученного из локализатора. Но при необходимости, это можно изменить, 
для хранения переводов по группам или по языкам. Это будет удобно, 
когда вы захотите получить все переводы для определенной формы, модуля
или группы сообщений об ошибках.

Предусмотрены 3 типа:
```
    // (по умолчанию) Сохраняется каждый перевод отдельно в кэш
    $cacheStorageType = CacheStorageTypes::TRANSLATIONS_STORAGE_TYPE();
    
    // Переводы сохраняются с группировкой по родительскому элементу
    $cacheStorageType = CacheStorageTypes::PARENTS_STORAGE_TYPE();
    
    // Переводы сохраняются с группировкой по коду языка
    $cacheStorageType = CacheStorageTypes::LANGUAGE_STORAGE_TYPE();
```

Чтобы изменить способ формирования ключа для кэширования, необходимо 
вызывать функцию:
```
    $translator->changeStorageType($cacheStorageType);
```

Переключать тип вы можете без ограничений, в процессе работы с переводами,
сохранив переводы в каждом из 3-х вариантов и потом обращаться к ним,
при необходимости, на том уровне, который будет более оптимален в каждом
конкретном случае. 

-------------------------------------

***Извлечь переводы из кэша***

1. При типе кэширования ` TRANSLATIONS_STORAGE_TYPE `:
```
    /** @var  \Ids\Localizator\DTO\StaticTranslationData $cashedData */
    $cachedData = $translator->getStaticItem('eng', 'Test form', 'Title');
    
    echo $cachedData->getTranslation();
```
результат:
```
    "Test form title" 
```

2. При типе кэширования ` PARENTS_STORAGE_TYPE `:
```
    /** @var  \Ids\Localizator\DTO\StaticTranslationData $cashedData */
    $cachedData = $translator->getStaticItem('rus');

    var_dump($cachedData->getTranslation()); 
```
результат:
```
    array:1 [
      "Title" => "Test form title"
    ]
```

3. При типе кэширования ` LANGUAGE_STORAGE_TYPE `:
```
    /** @var  \Ids\Localizator\DTO\StaticTranslationData $cashedData */
    $cachedData = $translator->getStaticItem('eng');

    var_dump($cachedData->getTranslation()); 
```
результат:
```
    array:1 [
      "Test form" => array:1 [
        "Title" => "Название тестовой формы"
      ]
    ]
```
------------------------------------
***Очистить кэш***

```
$translator->cacheClear();
```

-------------------------------------

Разное

```        
//использовать собственную реализацию кешера Psr\Cache\CacheItemPoolInterface

$translator = TranslatorFactory::create($applicationId, 'rus')
                ->setCache(new RedisAdapter(new \Redis()))
                ->build();

```


Некоторые команды для разработки

```
docker-compose run --rm php-cli composer --version
docker-compose run --rm php-cli composer install
```
