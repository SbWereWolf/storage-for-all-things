# storage-for-all-things

"Хранилище для всего" - исследовательский проект для разработки движка "Универсального каталога"

# Соглашения

The key words “MUST”, “MUST NOT”, “REQUIRED”, “SHALL”, “SHALL NOT”, “SHOULD”, “SHOULD NOT”, “RECOMMENDED”, “MAY”, and “OPTIONAL” in this document are to be interpreted as described in [RFC 2119](http://tools.ietf.org/html/rfc2119]).

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

## S001 Создать экземпляр с определёнными характеристиками

- S001A1S01 создать сущность
- S001A1S02 задать свойства сущности
- S001A1S03 создать характеристику
- S001A1S04 задать свойства характеристики
- S001A1S05 задать сушности характеристику
- S001A3S01 создать представление для экземпляров сущности
- S001A2S01 создать экземпляр сущности
- S001A2S02 задать значения для характеристики экземпляра
- S001A3S02 получить данные представления 

## S002 Найти экземпляры по определённому условию

- S002A3S03 определить возможные условия для поиска
- S002A3S04 сделать выборку экземпляров по заданным условиям поиска (поиск в представлении)

## E003 Создать структуру для быстрого поиска

- E003A4S01 сформировать запрос на создание материализованного представления и индексов
- E003A4S02 создать материализованное представление и индексы

## E004 Обновить данные для быстрого поиска

- E004A2S03 изменить значение характеристики экземпляра
- E004A4S03 обновить данные в материализованном представлении

## E005 Создать структуру для быстрого обновления

- E005A5S01 сформировать запрос на создание таблицы и индексов
- E005A5S02 создать таблицу и индексы
- E005A5S04 заполнить таблицу

## S006 Выполнить быстрое обновление

- E004A2S03 изменить значение характеристики экземпляра
- S006A5S03 обновить данные в таблице

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
data-type => value ( MUST be one of : decimal || timestamp || interval || symbol ) || not specified
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
data-type => SHOULD be one of : decimal || timestamp || interval || symbol
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
        'data-type' => decimal || timestamp || interval || symbol ,
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

GET '/thing-kind/filter/[{params:.*}]' search
```
params:
essence-code => strict equality || not specified
[kind-code=>filter] (filter MUST be one of [min,max] or [value1, value2, .. , valueN] )

result:
[ 
    essence-code  => [
            thing-code => [ kind-code => value ] 
        ]
]
```
# Пример использования API для реализации Услуг

## S001 Создавать экземпляр с определёнными характеристиками

## S001A1S01 создать сущность

## S001A1S02 задать свойства сущности

## S001A1S03 создать характеристику

## S001A1S04 задать свойства характеристики

## S001A1S05 задать сушности характеристику

## S001A3S01 создать представление для экземпляров сущности

## S001A2S01 создать экземпляр сущности

## S001A2S02 задать значения для характеристики экземпляра

## S001A3S02 получить данные представления 

## S002 Найти экземпляры по определённому условию

## S002A3S03 определить возможные условия для поиска

## S002A3S04 сделать выборку экземпляров по заданным условиям поиска (поиск в представлении)

## E003 Создать структуру для быстрого поиска

## E003A4S01 сформировать запрос на создание материализованного представления и индексов

## E003A4S02 создать материализованное представление и индексы

## E004 Обновить данные для быстрого поиска

## E004A2S03 изменить значение характеристики экземпляра

## E004A4S03 обновить данные в материализованном представлении

## E005 Создать структуру для быстрого обновления

## E005A5S01 сформировать запрос на создание таблицы и индексов

## E005A5S02 создать таблицу и индексы

## E005A5S04 заполнить таблицу

## S006 Выполнить быстрое обновление

## E004A2S03 изменить значение характеристики экземпляра

## S006A5S03 обновить данные в таблице
