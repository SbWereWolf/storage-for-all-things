# storage-for-all-things

"Хранилище для всего" - исследовательский проект для разработки движка "Универсального каталога"

# Модули

## A1 essence processing

обработка сущностей

## A2 content processing

обработка содержимого

## A3 search engine

движок поиска

## A4 rapid obtainment

быстрый поиска

## A5 rapid storage

быстрая запись

## A6 api handler

обработчик апи

## A7 outputting

вывод результатов поиска

# Услуги

## Создать сущность с характеристиками

- создать характеристику
- задать характеристике способ поиска
- создать сущность
- задать сушности характеристику

## Создавать экземпляр с определёнными характеристиками

- создать экземпляр сущности
- задать характеристикам значения

## Найти экземпляры по определённому условию

- определить допустимые условия
- задать значения условиям
- найти экземпляры по заданным условиям (прямым поиском, поиском в представлении, поиском в таблице)

## Дополнительные услуги

- сформировать запрос на создание представления и индексов
- создать представление
- обновить представление
- сформировать запрос на создание таблицы и индексов
- создать таблицу
- обновить таблицу
- подготовить данные для вывода

# API

## essence - Сущность (вид)

POST '/essence/add'

GET '/essence/{code}/get'

PUT '/essence/{code}/change'

GET '/essence-catalog/{code}/get'

GET '/essence-catalog/{code}/find/[{params:.*}]'

```
$params = array();
$len = count($array);
$len++;
$i = 0 ;
while($i+1 < $len){    
    $params[$array[$i]] = $array[$i+1];
    $i += 2;
}
```

## kind - Характеристика

POST '/kind/add'

GET '/kind/{code}/get'

PUT '/kind/{code}/change'

GET '/kind-catalog/{code}/get'

GET '/kind-catalog/{code}/find/[{params:.*}]'

## thing - Модель (экземпляр сущности)

POST '/thing/add'

GET '/thing/{code}/get'

PUT '/thing/{code}/change'

GET '/thing-catalog/{code}/get'

GET '/thing-catalog/{code}/find/[{params:.*}]'

## content - Показатель (значение характеристики)

POST '/content/thing/{thing-code}/kind/{kind-code}/add'

GET '/content/thing/{thing-code}/kind/{kind-code}/get'

PUT '/content/thing/{thing-code}/kind/{kind-code}/change'
