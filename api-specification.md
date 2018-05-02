# Введение

Документ описывает API AllThings.

# API

## essence - Сущность (тип для предметов)

POST '/essence/{code}' create

GET '/essence/{code}' get
```
result, fields of essence object:
code
title
remark
storage
```
PUT '/essence/{code}' change
```
parameters (any parameter can be omitted):
code => value
title => value
remark => value
storage => value ( MUST be one of : view | matherial view | table )
```

## essence-catalog - Каталог сущностей

GET '/essence-catalog' get
```
result, collection:
essence-code
```

GET '/essence-catalog/{search-parameters}' search
```
search-parameters (any parameter can be omitted):
code => %like%
title => %like%
remark => %like%
storage => SHOULD be one of : view | matherial view | table

result, collection:
essence-code
```
## attribute - Характеристика (для предмета)

POST '/attribute/{code}' create

GET '/attribute/{code}' get
```
result:
code
title
remark
data-type
range-type
```
PUT '/attribute/{code}' change
```
parameters (any parameter can be omitted):
code => value
title => value
remark => value
data-type => value ( MUST be one of : decimal | timestamp | symbol )
range-type => value ( MUST be one of : continuous | discrete )
```

## attribute-catalog - Каталог характеристик

GET '/attribute-catalog' get
```
result, collection:
attribute-code
```
GET '/attribute-catalog/filter/{search-parameters}' search
```
search-parameters (any parameter can be omitted):
code => %like%
title => %like%
remark => %like%
data-type => SHOULD be one of : decimal | timestamp | symbol
range-type => SHOULD be one of : continuous || discrete

result:
attribute-code
```
## essence-attribute - характеристики сущностей (набор атрибутов свойственных типу)

POST '/essence-attribute/{essence-code}/{attribute-code}' link essence with attribute

DELETE '/essence-attribute/{essence-code}/{attribute-code}' unlink essence and attribute

GET '/essence-attribute/{essence-code}' get attributes of essence
```
result, collection:
[
    essence-code => [attribute-code1, attribute-code2, .. , attribute-codeN]
]
```  
## thing - Предмет (экземпляр сущности)

POST '/thing/{essence-code}/{thing-code}' create

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
parameters (any parameter can be omitted):
code => value
title => value
remark => value
```

## essence-filer - Фильтры для поиска предметов определённого типа

GET '/essence-filer/{essence-code}' get
```
result, collection:
[
    attribute-code =>[
        'data-type' => SHALL BE one of: decimal | timestamp | symbol ,
        'range-type' => [
            'type' => SHALL BE one of: continuous | discrete ,
            'values' => SHALL BE one of: [min-value,max-value] for continuous | [value1, value2, .. , valueN] for discrete
        ]
    ]
] 
      
```

## thing-attribute (content) - Значение характеристики предмета (содержимое характеристики)

POST '/thing-attribute/{thing-code}/{attribute-code}' create

PUT '/thing-attribute/{thing-code}/{attribute-code}' change
```
parameter:
content => value 
```

GET '/thing-attribute/essence-code/filter/{essence-code}/{search-parameters}' search
```
parameters:
essence-code => strict equality
search-parameters => 
[
    attribute-code =>[
        'values' => SHALL BE one of:
               [min-value,max-value] for continuous 
            or [value1, value2, .. , valueN] for discrete
    ]
] 

result:
[ 
    essence-code  => [thing-code]
]
```
