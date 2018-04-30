# storage-for-all-things

"Хранилище для всего" - исследовательский проект для разработки движка "Универсального каталога"

# Соглашения

The key words “MUST”, “MUST NOT”, “REQUIRED”, “SHALL”, “SHALL NOT”, “SHOULD”, “SHOULD NOT”, “RECOMMENDED”, “MAY”, and “OPTIONAL” in this document are to be interpreted as described in [RFC 2119](http://tools.ietf.org/html/rfc2119]).

# Услуги (варианты использования)

## S001 Создать экземпляр с определёнными характеристиками

- S001A1S01 создать сущность
- S001A4S01 сформировать запрос на создание представления
- S001A4S02 создать представление
- S001A1S02 задать свойства сущности
- S001A1S03 создать характеристику
- S001A1S04 задать свойства характеристики
- S001A1S05 задать сушности характеристику
- S001A4S03 обновить представление
- S001A2S01 создать экземпляр сущности
- S001A2S02 задать значения для характеристики экземпляра
- S001A4S04 получить данные представления 

## S002 Найти экземпляры по определённому условию

- S002A4S03 определить возможные условия для поиска
- S002A4S04 сделать выборку экземпляров по заданным условиям поиска (поиск в представлении)

## E003 Создать структуру для быстрого поиска

- E003A5S01 сформировать запрос на создание материализованного представления и индексов
- E003A5S02 создать материализованное представление и индексы

## E004 Обновить данные для быстрого поиска

- E004A2S03 изменить значение характеристики экземпляра
- E004A5S03 обновить данные в материализованном представлении

## S005 Выполнить быстрый поиск

- S002A5S04 определить возможные условия для поиска
- S002A5S05 сделать выборку экземпляров по заданным условиям поиска (поиск в представлении)

## E006 Создать структуру для быстрого обновления

- E006A6S01 сформировать запрос на создание таблицы и индексов
- E006A6S02 создать таблицу и индексы
- E006A6S04 заполнить таблицу

## S007 Выполнить быстрое обновление

- E004A2S03 изменить значение характеристики экземпляра
- S007A6S03 обновить данные в таблице

# API

```
$params = array();
$len = count($array);
$len += 1;
$i = 0 ;
while($i+1 < $len){    
    $params[$array[$i]] = $array[$i+1];
    $i += 2;
}
```

## essence - Сущность (вид)

POST '/essence/{code}' add

GET '/essence/{code}' get
```
result:
code
title
remark
storage
```
PUT '/essence/{code}' change
```
params:
code => value || not specified
title => value || not specified
remark => value || not specified
storage => value ( MUST be one of : view || matherial view || table ) || not specified
```

## essence-catalog - Каталог видов

GET '/essence-catalog' get
```
result:
code
title
remark
storage
```

GET '/essence-catalog/filter/[{params:.*}]' search
```
params:
code => %like%
title => %like%
remark => %like%
storage => SHOULD be one of : view || matherial view || table

result:
essence-code
```
## kind - Характеристика

POST '/kind/{code}' add

GET '/kind/{code}' get
```
result:
code
title
remark
data-type
range-type
```
PUT '/kind/{code}' change
```
params:
code => value || not specified
title => value || not specified
remark => value || not specified
data-type => value ( MUST be one of : decimal || timestamp || symbol ) || not specified
range-type => value ( MUST be one of : continuous || discrete ) || not specified
```

## kind-catalog - Каталог характеристик

GET '/kind-catalog' get
```
result:
kind-code
kind-title
kind-remark
```
GET '/kind-catalog/filter/[{params:.*}]' search
```
params:
code => %like%
title => %like%
remark => %like%
data-type => SHOULD be one of : decimal || timestamp || symbol
range-type => SHOULD be one of : continuous || discrete

result:
kind-code
```
## essence-kind - характеристики видов

POST '/essence-kind/{essence-code}/{kind-code}' link essence with kind

DELETE '/essence-kind/{essence-code}/{kind-code}' unlink essence and kind

GET '/essence-kind[/{essence-code}]' search
```
result:
[
    essence-code => [kind-code1, kind-code2, .. , kind-codeN]
]
```  
## thing - Модель (экземпляр сущности)

POST '/thing/{essence-code}/{thing-code}' add

GET '/thing/{code}' get
```
result:
essence-code
thing-code
thing-title
thing-remark
```
PUT '/thing/{code}' change
```
params:
code => value || not specified
title => value || not specified
remark => value || not specified
```

## essence-filer - Фильтры для вида (для экземпляров сущности)

GET '/essence-filer/{essence-code}' get
```
result:
[kind-code =>
    [
        'data-type' => decimal || timestamp || symbol ,
        range-type => continuous || discrete ,
        'values' => [min,max] || [value1, value2, .. , valueN] ,
    ]
] 
      
```

## thing-kind - Значение характеристики модели

POST '/thing-kind/{thing-code}/{kind-code}' add

PUT '/thing-kind/{thing-code}/{kind-code}' change
```
params:
value => value || not specified 
```

GET '/thing-kind/filter/essence-code/{essence-code}[/{params:.*}]' search
```
params:
essence-code => strict equality
[kind-code=>filter] (filter MUST be one of [min,max] or [value1, value2, .. , valueN] )

result:
[ 
    essence-code  => [
            thing-code => [ kind-code => value ] 
        ]
]
```
# Пример использования API для реализации Услуг

## S001 Создать экземпляр с определёнными характеристиками

## S001A1S01 создать сущность
```
POST '/essence/cake'
```
## S001A1S02 задать свойства сущности
```
PUT '/essence/cake'
{
  "title": "булочка",
  "storage": "view"
}
```
## S001A1S03 создать характеристику
```
POST '/kind/price'
POST '/kind/production-date'
POST '/kind/place-of-production'
```
## S001A1S04 задать свойства характеристики
```
PUT '/kind/price'
{
  "title": "цена, руб.",
  "data-type": "decimal",
  "range-type": "continuous"
}
PUT '/kind/production-date'
{
  "title": "дата выработки",
  "data-type": "timestamp",
  "range-type": "continuous"
}
PUT '/kind/place-of-production'
{
  "title": "Место производства",
  "data-type": "symbol",
  "range-type": "discrete"
}
```
## S001A1S05 задать сушности характеристику
```
POST '/essence-kind/cake/price'
POST '/essence-kind/cake/production-date'
POST '/essence-kind/cake/place-of-production'
```

## S001A2S01 создать экземпляр сущности
```
POST '/thing/cake/bun-with-jam'
POST '/thing/cake/bun-with-raisins'
POST '/thing/cake/cinnamon-bun'
```
## S001A2S02 задать свойства экземпляра
```
PUT '/thing/bun-with-jam'
{
  "title": "Булочка с повидлом"
}
PUT '/thing/bun-with-raisins'
{
  "title": "Булочка с изюмом"
}
PUT '/thing/cinnamon-bun'
{
  "title": "Булочка с корицей"
}
```
## S001A2S03 задать значения для характеристики экземпляра
```
PUT '/thing-kind/bun-with-jam/price'
{
  "value": 15.50
}
PUT '/thing-kind/bun-with-jam/production-date'
{
  "value": "20180429T1356"
}
PUT '/thing-kind/bun-with-jam/place-of-production'
{
  "value": "Екатеринбург"
}
PUT '/thing-kind/bun-with-raisins/price'
{
  "value": 9.50
}
PUT '/thing-kind/bun-with-raisins/production-date'
{
  "value": "20180427"
}
PUT '/thing-kind/bun-with-raisins/place-of-production'
{
  "value": "Екатеринбург"
}
PUT '/thing-kind/cinnamon-bun/price'
{
  "value": 4.50
}
PUT '/thing-kind/cinnamon-bun/production-date'
{
  "value": "20180429"
}
PUT '/thing-kind/cinnamon-bun/place-of-production'
{
  "value": "Челябинск"
}
```
## S001A4S04 получить данные представления
```
GET '/thing-kind/filter/essence-code/cake'
result:
{
  "cake": [
    {
      "bun-with-jam": [
        {
          "price": 15.5
        },
        {
          "production-date": "20180429T1356"
        },
        {
          "place-of-production": "Екатеринбург"
        }
      ],
      "bun-with-raisins": [
        {
          "price": 9.5
        },
        {
          "production-date": "20180427"
        },
        {
          "place-of-production": "Екатеринбург"
        }
      ],
      "cinnamon-bun": [
        {
          "price": 4.5
        },
        {
          "production-date": "20180429"
        },
        {
          "place-of-production": "Челябинск"
        }
      ]
    }
  ]
}
``` 
## S002A4S03 определить возможные условия для поиска
``` 
GET '/essence-filer/cake'
result:
{
  "cake": [
    {
      "price": [
        {
          "data-type": "decimal"
        },
        {
          "range-type": "continuous"
        },
        {
          "values": [
            4.5,
            15.5
          ]
        }
      ],
      "production-date": [
        {
          "data-type": "timestamp"
        },
        {
          "range-type": "continuous"
        },
        {
          "values": [
            "20180427",
            "20180429T1356"
          ]
        }
      ],
      "place-of-production": [
        {
          "data-type": "symbol"
        },
        {
          "range-type": "discrete"
        },
        {
          "values": [
            "Екатеринбург",
            "Челябинск"
          ]
        }
      ]
    }
  ]
}

``` 
## S002A4S04 сделать выборку экземпляров по заданным условиям поиска (поиск в представлении)
```
GET '/thing-kind/filter/essence-code/cake/price/4.5/price/10/production-date/20180427/production-date/20180429/place-of-production/Екатеринбург/place-of-production/Челябинск'
result:
{
  "cake": [
    "bun-with-raisins",
    "cinnamon-bun"
  ]
}
```
# Модули

## A1 essence processing

обработка сущностей

## A2 content processing

обработка содержимого

## A3 search engine

движок поиска

## A4 primitive obtainment

примитивный поиск (view)

## A5 rapid obtainment

быстрый поиска (materialized view)

## A6 rapid storage

быстрая запись (table)

## A7 outputting

вывод результатов поиска
