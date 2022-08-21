# Подготовка к использованию библиотеки

## Оглавление
- [Введение](./../readme.md)
- [Варианты использования](./use-case.md)
- [Преимущества от использования](./benefits.md)
- [Подготовка к использованию](./preparatory-steps.md)
- [Архитектура](./inner-workings.md)
- [Дополнительные материалы](./additional.md)

## Создать пользователя для работы с БД

1. Создать базу данных в СУБД PostgreSQL 9+ ;
2. Подключиться к СУБД и созданной БД;
3. Выполнить скрипт `/configuration/install-tables.sql.example`, в
   БД будут созданы таблицы необходимые для записи и чтения данных;
4. Создать пользователя СУБД для работы с БД от имени библиотеки;
5. Пользователь должен иметь права на создание представлений,
   материализованных представлений и таблиц, соответственно на CRUD
   операции с этими объектами БД;

## Как проверить работоспособность

### Настроить подключение к БД

Библиотека для работы с СУБД использует PDO. Тесты используют
реквизиты подключения к СУБД из файла
`/корень библиотеки/configuration/pdo.env`.

Соответственно для запуска тестов вам необходимо создать файл с именем
`pdo.env`, расположенный по пути `/корень библиотеки/configuration/`.
Либо можно заменить в тестах код реализующий создание подключения,
для этого вы можете найти следующий строки:

```php
        $pathParts = [
            __DIR__,
            '..',
            '..',
            'configuration',
            'pdo.env',
        ];
        $path = implode(DIRECTORY_SEPARATOR, $pathParts);
        $linkToData = (new PdoConnection($path))->get();
```

И их заменить на такие:

```php
        $linkToData = new PDO('pgsql:dbname=all_things;host=localhost');
```

Если вы решите создать собственный конфигурационный файл
(`/configuration/pdo.env`), то за основу можно взять
`/configuration/pdo.env.example`.

### Запустить тесты

Для тестирования кода написан один функциональный тест на PHPUnit:
tests/Integration/AutomatedProcessTest.php

Для тестирования используйте команду

```bash
composer test
```

Результат тестирования будет выглядеть примерно следующим образом:

```bash
> php ./vendor/phpunit/phpunit/phpunit --coverage-html build/coverage-report --debug
PHPUnit 9.5.11 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.1.2
Configuration: E:\WORK\storage-for-all-things\phpunit.xml
Warning:       XDEBUG_MODE=coverage or xdebug.mode=coverage has to be set

Test 'Integration\AutomatedProcessTest::testInit' started
Test 'Integration\AutomatedProcessTest::testInit' ended
Test 'Integration\AutomatedProcessTest::testBlueprintCreate' started
Test 'Integration\AutomatedProcessTest::testBlueprintCreate' ended
Test 'Integration\AutomatedProcessTest::testKindCreate' started
Test 'Integration\AutomatedProcessTest::testKindCreate' ended
Test 'Integration\AutomatedProcessTest::testDefineBlueprint' started
Test 'Integration\AutomatedProcessTest::testDefineBlueprint' ended
Test 'Integration\AutomatedProcessTest::testCreateItem' started
Test 'Integration\AutomatedProcessTest::testCreateItem' ended
Test 'Integration\AutomatedProcessTest::testCreateContent' started
Test 'Integration\AutomatedProcessTest::testCreateContent' ended
Test 'Integration\AutomatedProcessTest::testCreateView' started
Test 'Integration\AutomatedProcessTest::testCreateView' ended
Test 'Integration\AutomatedProcessTest::testShowAllFromView' started
Test 'Integration\AutomatedProcessTest::testShowAllFromView' ended
Test 'Integration\AutomatedProcessTest::testGetFiltersForView' started
Test 'Integration\AutomatedProcessTest::testGetFiltersForView' ended
Test 'Integration\AutomatedProcessTest::testSearchWithinView' started
Test 'Integration\AutomatedProcessTest::testSearchWithinView' ended
Test 'Integration\AutomatedProcessTest::testCreateMathView' started
Test 'Integration\AutomatedProcessTest::testCreateMathView' ended
Test 'Integration\AutomatedProcessTest::testShowAllFromMathView' started
Test 'Integration\AutomatedProcessTest::testShowAllFromMathView' ended
Test 'Integration\AutomatedProcessTest::testGetFiltersForMathView' started
Test 'Integration\AutomatedProcessTest::testGetFiltersForMathView' ended
Test 'Integration\AutomatedProcessTest::testSearchWithinMathView' started
Test 'Integration\AutomatedProcessTest::testSearchWithinMathView' ended
Test 'Integration\AutomatedProcessTest::testCreateTable' started
Test 'Integration\AutomatedProcessTest::testCreateTable' ended
Test 'Integration\AutomatedProcessTest::testShowAllFromTable' started
Test 'Integration\AutomatedProcessTest::testShowAllFromTable' ended
Test 'Integration\AutomatedProcessTest::testGetFiltersForTable' started
Test 'Integration\AutomatedProcessTest::testGetFiltersForTable' ended
Test 'Integration\AutomatedProcessTest::testSearchWithinTable' started
Test 'Integration\AutomatedProcessTest::testSearchWithinTable' ended
Test 'Integration\AutomatedProcessTest::testAddNewItem' started
Test 'Integration\AutomatedProcessTest::testAddNewItem' ended
Test 'Integration\AutomatedProcessTest::testAddNewItemToView' started
Test 'Integration\AutomatedProcessTest::testAddNewItemToView' ended
Test 'Integration\AutomatedProcessTest::testAddNewItemToMathView' started
Test 'Integration\AutomatedProcessTest::testAddNewItemToMathView' ended
Test 'Integration\AutomatedProcessTest::testAddNewItemToTable' started
Test 'Integration\AutomatedProcessTest::testAddNewItemToTable' ended
Test 'Integration\AutomatedProcessTest::testAddNewKind' started
Test 'Integration\AutomatedProcessTest::testAddNewKind' ended
Test 'Integration\AutomatedProcessTest::testAddNewKindToView' started
Test 'Integration\AutomatedProcessTest::testAddNewKindToView' ended
Test 'Integration\AutomatedProcessTest::testAddNewKindToMathView' started
Test 'Integration\AutomatedProcessTest::testAddNewKindToMathView' ended
Test 'Integration\AutomatedProcessTest::testAddNewKindToTable' started
Test 'Integration\AutomatedProcessTest::testAddNewKindToTable' ended
Test 'Integration\AutomatedProcessTest::testChangeContent' started
Test 'Integration\AutomatedProcessTest::testChangeContent' ended
Test 'Integration\AutomatedProcessTest::testChangeContentWithinView' started
Test 'Integration\AutomatedProcessTest::testChangeContentWithinView' ended
Test 'Integration\AutomatedProcessTest::testChangeContentWithinMathView' started
Test 'Integration\AutomatedProcessTest::testChangeContentWithinMathView' ended
Test 'Integration\AutomatedProcessTest::testChangeContentWithinTable' started
Test 'Integration\AutomatedProcessTest::testChangeContentWithinTable' ended
Test 'Integration\AutomatedProcessTest::testUnlinkKind' started
Test 'Integration\AutomatedProcessTest::testUnlinkKind' ended
Test 'Integration\AutomatedProcessTest::testUnlinkKindWithView' started
Test 'Integration\AutomatedProcessTest::testUnlinkKindWithView' ended
Test 'Integration\AutomatedProcessTest::testUnlinkKindWithMathView' started
Test 'Integration\AutomatedProcessTest::testUnlinkKindWithMathView' ended
Test 'Integration\AutomatedProcessTest::testUnlinkKindWithTable' started
Test 'Integration\AutomatedProcessTest::testUnlinkKindWithTable' ended
Test 'Integration\AutomatedProcessTest::testRemoveItem' started
Test 'Integration\AutomatedProcessTest::testRemoveItem' ended
Test 'Integration\AutomatedProcessTest::testRemoveItemWithView' started
Test 'Integration\AutomatedProcessTest::testRemoveItemWithView' ended
Test 'Integration\AutomatedProcessTest::testRemoveItemWithMathView' started
Test 'Integration\AutomatedProcessTest::testRemoveItemWithMathView' ended
Test 'Integration\AutomatedProcessTest::testRemoveItemWithTable' started
Test 'Integration\AutomatedProcessTest::testRemoveItemWithTable' ended
Test 'Integration\AutomatedProcessTest::testRemoveCategory' started
Test 'Integration\AutomatedProcessTest::testRemoveCategory' ended
Test 'Integration\AutomatedProcessTest::testFinally' started
Test 'Integration\AutomatedProcessTest::testFinally' ended


Time: 00:00.488, Memory: 6.00 MB

OK (40 tests, 132 assertions)
```