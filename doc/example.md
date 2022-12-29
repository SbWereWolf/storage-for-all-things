# Примеры использования

## Оглавление

- [Введение](./../readme.md)
- [Примеры использования](./example.md)
- [Варианты использования](./use-case.md)
- [Преимущества от использования](./benefits.md)
- [Подготовка к использованию](./preparatory-steps.md)
- [Архитектура](./inner-workings.md)
- [Дополнительные материалы](./additional.md)

Статья на Хабре с комментариями к примерам использования
[по ссылке](https://habr.com/ru/post/599639/)

## Создать атрибуты и из них создать категорию

```php
use Environment\Database\PdoConnection;
use AllThings\ControlPanel\Operator;

$pathParts = [__DIR__, 'configuration', 'pdo.env',];
$path = implode(DIRECTORY_SEPARATOR, $pathParts);

/** @var PDO $conn Объект для работы с СУБД */
$conn = (new PdoConnection($path))->get();

/* класс для работы с элементами каталога: сущность, атрибут, значение */
$operator = new Operator($conn);

/* создаём строковый атрибут */
$attribute1 = $operator->createKind(
    'discrete_symbols',
    'word',
    'discrete',
);
/* создаём числовой атрибут */
$attribute2 = $operator->createKind(
    'analog_numbers',
    'number',
    'continuous',
);

/* создаём сущность */
$essence = $operator->createBlueprint('item');

/* зададим атрибуты для сущности */
$operator->attachKind(
    'item',
    'discrete_symbols',
);

$operator->attachKind(
    'item',
    'analog_numbers',
);
```

## Создать позицию в категории

```php
use Environment\Database\PdoConnection;
use AllThings\ControlPanel\Operator;

$pathParts = [DIR, 'configuration', 'pdo.env',];
$path = implode(DIRECTORY_SEPARATOR, $pathParts);
/** @var PDO $conn Объект для работы с СУБД */
$conn = (new PdoConnection($path))->get();

/* класс для работы с элементами каталога: сущность, атрибут, значение */
$operator = new Operator($conn);

/* создадим позицию каталога, добавим "товар" в категорию */
$operator->createItem(
    'item',
    'position',
);

/* зададим значения для атрибутов позиции,
введём значения для характеристик "товара" */

$operator->changeContent(
    'position',
    'discrete_symbols',
    'красный'
);

$operator->changeContent(
    'position',
    'analog_numbers',
    '1234567890.1234'
);
```

## Создать материализованное представление и таблицу для категории

```php
use Environment\Database\PdoConnection;
use AllThings\ControlPanel\Schema;

$pathParts = [__DIR__, 'configuration', 'pdo.env',];
$path = implode(DIRECTORY_SEPARATOR, $pathParts);
$pdo = (new PdoConnection($path))->get();

/* объект для работы с источником данных конкретной сущности */
$schema = new Schema($pdo, 'item');

/* создадим материализованное представление для сущности
 и назначим его как источник данных для сущности */
$schema->handleWithRapidObtainment();

/* создадим таблицу для сущности
и назначим её как источник данных для сущности */
$schema->handleWithRapidRecording();
```

## Получить границы для условий поиска

```php
use Environment\Database\PdoConnection;
use AllThings\ControlPanel\Browser;

$pathParts = [__DIR__, 'configuration', 'pdo.env',];
$path = implode(DIRECTORY_SEPARATOR, $pathParts);
$pdo = (new PdoConnection($path))->get();

/* объект для просмотра данных */
$browser = new Browser($pdo);

/* получим границы поиска, допустимые значения фильтров */
$filters = $browser->filters('item');

/*
$filters будет содержать:
array (
  0 => 
  AllThings\SearchEngine\DiscreteFilter::__set_state(array(
     'values' => 
    array (
      0 => 'красный',
    ),
     'attribute' => 'discrete_symbols',
  )),
  1 => 
  AllThings\SearchEngine\ContinuousFilter::__set_state(array(
     'min' => '1234567890.1234',
     'max' => '1234567890.1234',
     'attribute' => 'analog_numbers',
  )),
)
*/
```

## Задать условия поиска и получить результат

```php
use Environment\Database\PdoConnection;
use AllThings\ControlPanel\Browser;
use AllThings\SearchEngine\ContinuousFilter;
use AllThings\SearchEngine\DiscreteFilter;

$pathParts = [__DIR__, 'configuration', 'pdo.env',];
$path = implode(DIRECTORY_SEPARATOR, $pathParts);
$pdo = (new PdoConnection($path))->get();

$browser = new Browser($pdo);

$numbersFilter = new ContinuousFilter(
    'analog_numbers',
    '0',
    '999999999.9999',
);

$wordsFilter = new DiscreteFilter(
    'discrete_symbols',
    ['красный']
);

$filters = [$numbersFilter, $wordsFilter];

/* выполним поиск*/
$result = $browser->filterData('item', $filters);

/*
содержимое $result 
array (
  0 => 
  array (
    'thing_id' => 1,
    'code' => 'position',
    'discrete_symbols' => 'красный',
    'analog_numbers' => '1234567890.1234',
  ),
)
*/
```

## Добавить новый атрибут

```php
use Environment\Database\PdoConnection;
use AllThings\ControlPanel\Operator;
use AllThings\ControlPanel\Schema;

$pathParts = [DIR, 'configuration', 'pdo.env',];
$path = implode(DIRECTORY_SEPARATOR, $pathParts);
$pdo = (new PdoConnection($path))->get();

$operator = new Operator($pdo);

/ создаём новый атрибут */
$attribute3 = $operator->createKind(
    'discrete_numbers',
    'number',
    'discrete',
);

/* зададим новый атрибут для сущности */
$operator->attachKind(
    'item',
    'discrete_numbers',
);

/* объект для управления данными сущности */
$schema = new Schema($pdo, 'item');

/* Если мы используем в качестве быстрого источника данных
материализованное представление,
то нам необходимо его пересоздать
*/

/* пересоздадим материализованное представление */
$schema->setup($attribute3);
/* в материализованное представление добавиться новая колонка */
/* DTO атрибута - $attribute3, при работе через материализованное представление,
можно не передавать */

/* Если мы используем в качестве быстрого источника данных
таблицу,
то нам необходимо добавить колонку для нового атрибута
*/

/* Добавим колонку для нового атрибута */
$schema->setup($attribute3);
/* в таблице добавиться новая колонка */
/* DTO атрибута - $attribute3, при работе через таблицу,
лучше передать, тогда будет добавлена только одна колонка,
иначе будет пересоздана вся таблица */

/* Что бы в дальнейшем, при добавлении новых позиций в таблицу,
не было проблем из-за разного количества колонок в представлении и в таблице,
необходимо обновить представление.
Для этого надо поменять способ доступа к данным у объекта сущности 
и после этого обновить представление. */

/* изменяем способ доступа к данным сущности */
$schema->changeStorage('view');

/* пересоздаём представление */
$schema->setup();

/* Создадим значения для нового атрибута для всех ранее созданных моделей */
/* будет создана запись для Value - Значения - в EAV таблицах */
$operator->expandItem(
    'position',
    'discrete_numbers',
    '-1'
);
/* Если этого не сделать, то присвоить (или обновить) Значение для Атрибута
 не получиться, потому что фактически значение не было создано
 и в БД нет записи для обновления */

/* после этого надо вернуть способ доступа */
/* материализованное представление */
$schema->changeStorage('materialized view');
/* таблица */
$schema->changeStorage('table');

/* при этом пересоздавать или "освежать" источник данных не надо */
```

## Добавить новую позицию

```php
use Environment\Database\PdoConnection;
use AllThings\ControlPanel\Operator;
use AllThings\ControlPanel\Schema;

$pathParts = [__DIR__, 'configuration', 'pdo.env',];
$path = implode(DIRECTORY_SEPARATOR, $pathParts);
$pdo = (new PdoConnection($path))->get();

/* объект для работы с сущностями, атрибутами, значениями */
$operator = new Operator($pdo);

/* создаём новую позицию */
$thing = $operator->createItem(
    'item',
    'new_position',
);

/* объект для управления данными сущности */
$schema = new Schema($pdo, 'item');

/* не зависимо от того
материализованное представление или
таблица,
выбраны в качестве способа доступа к данным сущности,
достаточно выполнить "освежение" источника данных */
$schema->refresh();
/* если способ доступа материализованное представление, то оно будет пересчитано
если способ доступа - таблица, то новая запись будет добавлена из представления,
поэтому важно, что бы в представлении и в таблице были одни и те же колонки */
```

## Обновить значения атрибутов

```php
use Environment\Database\PdoConnection;
use AllThings\ControlPanel\Operator;
use AllThings\ControlPanel\Schema;
use AllThings\DataAccess\Crossover\Crossover;

$pathParts = [__DIR__, 'configuration', 'pdo.env',];
$path = implode(DIRECTORY_SEPARATOR, $pathParts);
$pdo = (new PdoConnection($path))->get();

/* оператор для работы с сущностями, атрибутами, значениями */
$operator = new Operator($pdo);

/* Если способ доступа к данным сущности - материализованное представление,
то нам необходимо обновить данные в таблицах EAV.
Если способ доступа к данным - таблица,
то обновлять таблицы EAV не надо, они будут обновлены вместе с данными таблицы
*/

/* обновляем значение атрибута у конкретной позиции в таблицах EAV */
$operator->changeContent(
    'new_position',
    'discrete_numbers',
    '0',
);

/* объект для управления данными сущности */
$schema = new Schema($pdo, 'item');

/* Если способ доступа к данным сущности - материализованное представление,
то его надо освежить */
$schema->refresh();

/* Если способ доступа к данным сущности - таблица,
то нам для того что бы освежить данные в таблице,
достаточно отдать массив новых значений,
для каждой позиции требуется отдельный вызов Schema::refresh(array $values = [])
*/

$value = 
    (new Crossover())
    ->setLeftValue('new_position')
    ->setRightValue('discrete_numbers')
    ->setContent('0');

$schema->refresh([$value]);
```
