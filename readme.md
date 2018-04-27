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

- создать сущность
- задать свойства сущности
- создать характеристику
- задать свойства характеристики
- задать сушности характеристику

## Создавать экземпляр с определёнными характеристиками

- создать экземпляр сущности
- задать значения для характеристики экземпляра 

## Найти экземпляры по определённому условию

- определить перечень условий
- задать значения условиям
- найти экземпляры по заданным условиям (прямым поиском, поиском в представлении, поиском в таблице)

## Дополнительные услуги

- сформировать запрос на создание представления и индексов
- создать представление
- обновить представление
- сформировать запрос на создание таблицы и индексов
- создать таблицу
- обновить таблицу

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
```
PUT '/essence/{code}' change
```
params:
code => value || not specified
title => value || not specified
remark => value || not specified
```

## essence-catalog - Каталог видов

GET '/essence-catalog' get
```
result:
code
title
remark
```

GET '/essence-catalog/filter/[{params:.*}]' search
```
params:
code => %like%
title => %like%
remark => %like%

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
data-type is one of : decimal || timestamp || interval || symbol
range-type is one of : continuous || discrete
```
PUT '/kind/{code}' change
```
params:
code => value || not specified
title => value || not specified
remark => value || not specified
data-type => value ( is one of : decimal || timestamp || interval || symbol ) || not specified
range-type => value ( is one of : continuous || discrete ) || not specified
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
data-type => decimal || timestamp || interval || symbol
range-type => continuous || discrete

result:
kind-code
```
## essence-kind - характеристики видов

POST '/essence-kind/{essence-code}/{kind-code}' link essence with kind

DELETE '/essence-kind/{essence-code}/{kind-code}' unlink essence and kind

GET '/essence-kind/filter/[{params:.*}]' search
```
params:
essence-code => strict equality || not specified

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
[kind-code=>filter] (filter is one of [min,max] or [value1, value2, .. , valueN] )

result:
[ 
    essence-code  => [
            thing-code => [ kind-code => value ] 
        ]
]
```
