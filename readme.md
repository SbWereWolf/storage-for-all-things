# STORAGE-FOR-ALL-THINGS
"Хранилище для всего" - это преимущества EAV, без недостатков EAV.

```bash
composer require sbwerewolf/eav-manager
```

## Оглавление
- [Кратчайшее описание функционала библиотеки](/readme.md)
- [Бизнес логика в общих словах](/inner-workings.md)

## Преимущества проекта "Хранилище для всего"

### Преимущества EAV

EAV - Entity Attribute Value - Сущность Атрибут Значение - это модель
данных, предназначенная для описания сущностей, в которых количество
атрибутов (свойств, параметров), характеризующих их является
произвольным и в любой момент времени, может быть как расширено, так
и сокращено.

Таким образом для каждой сущности можно иметь произвольный состав
атрибутов, и хранить любую информацию. 

### Недостатки EAV

Обычный способ использования EAV это построение представления общего
для всех сущностей. Это приводит к тому что при увеличении количества
сущностей и атрибутов, увеличивается объём данных в которых
выполняется поиск.

### Как "Хранилище для всего" устраняет недостатки EAV

Каждая сущность храниться в отдельной таблице или материализованном
представлении, при этом мастер данные сохраняются в таблицах EAV.

Все выборки данных выполняются не по таблицам EAV, а по данным
выделенным в отдельный объект БД. 

---

**СКОРОСТЬ РАБОТЫ С ДАННЫМИ СОХРАНЯЕТСЯ ПРИ ЛЮБОМ РОСТЕ КОЛИЧЕСТВА
СУЩНОСТЕЙ И ИХ АТРИБУТОВ.**

---

У "Хранилища для всего" сохранена гибкость и простота в управлении
атрибутивным составом данных, при этом сохранена скорость работы на
уровне работы с обычными таблицами.

Имея мастер данные, всегда можно изменить формат хранилища для
отдельной сущности, перейти на работу через таблицу БД или через
материализованное представление.

В будущем будет добавлена поддержка хранилища в формате JSONB.

## Как развернуть
1. Создать базу данных в СУБД PostgreSQL 9+ ;
2. Подключиться к СУБД и созданной БД;
3. Выполнить скрипт `configuration/install-tables.sql.example`, в
БД будут созданы таблицы необходимые для хранения данных;
4. Создать файл `configuration/pdo.env` (за основу взять
   `configuration/pdo.env.example`), пользователь должен иметь права
на создание представлений, материализованных представлений и
таблиц, соответственно на CRUD операции;

## Услуги (варианты использования)

### Основные услуги

- S001 Создать модель (предмет) с произвольными характеристиками
- S002 Выполнить поиск моделей по заданным характеристикам
- S003 Выполнить быстрый поиск
- S004 Выполнить быстрое обновление

### Дополнительные услуги

- E005 Создать структуру для быстрого поиска
- E006 Обновить данные для быстрого поиска
- E007 Создать структуру для быстрого обновления

## Дополнительные материалы

Для демонстрации работы с каталогом разработан
[тест](/tests/Integration/AutomatedProcessTest.php) по комментарии в
коде можно понять как работать с библиотекой.

Комплексные операции с данными удобно выполнять с помощью 
[Менеджера EAV](src/AllThings/ControlPanel/Manager.php).

Для генерации случайного набора данных написан скрипт
`data-generator/generate.php`.

Для замера производительности написан скрипт
`data-generator/benchmark.php`, с выводом среднего времени выполнения
по каждой операции с данными.

`benchmark.php` это в том числе и пример использования библиотеки.

Если у вас есть вопросы или вы хотите принять участие в разработке, то
мои контакты указаны ниже.

## Контакты
```
Вольхин Николай
e-mail ulfnew@gmail.com
phone +7-902-272-65-35
Telegram @sbwerewolf
```

[Telegram chat with me](https://t.me/SbWereWolf) 
