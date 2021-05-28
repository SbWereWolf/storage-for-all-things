# Введение
Документ описывает API AllThings.
# Оглавление
- [Пример использования API](/readme.md)
- [Бизнес логика в общих словах](/inner-workings.md)
- [Описание API](/api-specification.md)
# Соглашения

The key words “MUST”, “MUST NOT”, “REQUIRED”, “SHALL”, “SHALL NOT”,
“SHOULD”, “SHOULD NOT”, “RECOMMENDED”, “MAY”, and“OPTIONAL” in this
document are to be interpreted as described in
[RFC 2119](http://tools.ietf.org/html/rfc2119]).


# API AllThings

## essence - Сущность (тип для предметов)

POST '/essence/{code}' create

GET '/essence/{code}' get
```
result MUST be fields of the essence object:
code
title
remark
store-at
```
PUT '/essence/{code}' change
```
body MUST have values for fields of the essence object :
code => value
title => value
remark => value
store-at => value ( MUST be one of : view | matherial view | table )
```
## essence-catalog - Каталог сущностей

GET '/essence-catalog' get
```
result MUST be whole collection of:
essence-code
```
GET '/essence-catalog/filter/{filter}' search
```
filter (any parameter MAY be omitted):
code => %like%
title => %like%
remark => %like%
store-at => MUST be one of : view | matherial view | table

result MUST be collection of:
essence-code
```
## attribute - Характеристика (для предмета)

POST '/attribute/{code}' create

GET '/attribute/{code}' get
```
result MUST be fields of the attribute object:
code
title
remark
data-type
range-type
```
PUT '/attribute/{code}' change
```
body MUST have values for fields of the attribute object :
code => value
title => value
remark => value
data-type => value ( MUST be one of : decimal | timestamp | symbol )
range-type => value ( MUST be one of : continuous | discrete )
```
## attribute-catalog - Каталог характеристик

GET '/attribute-catalog' get
```
result MUST be whole collection of:
attribute-code
```
GET '/attribute-catalog/filter/{filter}' search
```
filter (any parameter MAY be omitted):
code => %like%
title => %like%
remark => %like%
data-type => MUST be one of : decimal | timestamp | symbol
range-type => MUST be one of : continuous || discrete

result MUST be collection of:
attribute-code
```
## essence-attribute - характеристики сущностей (набор атрибутов свойственных типу предметов)

POST '/essence-attribute/{essence-code}/{attribute-code}' link essence
with attribute

DELETE '/essence-attribute/{essence-code}/{attribute-code}' unlink
essence and attribute

GET '/essence-attribute/{essence-code}' get all attributes of essence
```
result MUST be collection of :
[
    attribute-code
]
```
## thing - Предмет (экземпляр сущности)

POST '/thing/{code}' create

GET '/thing/{code}' get
```
result MUST be fields of the thing object :
code
title
remark
```
PUT '/thing/{code}' change
```
body MUST have values for fields of the thing object :
code => value
title => value
remark => value
```
## essence-thing - предметы сущностей (набор предметов одного типа)

POST '/essence-thing/{essence-code}/{thing-code}' link essence with
thing

DELETE '/essence-thing/{essence-code}/{thing-code}' unlink essence and
thing

GET '/essence-thing/{essence-code}' get all thing of essence
```
result MUST be like this :
[
    essence-code => [thing-code1, thing-code2, .. , thing-codeN]
]
```
## essence-filer - Фильтры для поиска предметов определённого типа

GET '/essence-filer/{essence-code}' get
```
result MUST be like this :
[
    attribute-code =>[
        'data-type' => MUST be one of: decimal | timestamp | symbol ,
        'range-type' => [
            'type' => MUST be one of: continuous | discrete ,
            'values' => MUST be one of: [min-value,max-value] for continuous | [value1, value2, .. , valueN] for discrete
        ]
    ]
] 
```
## thing-attribute (content) - Значение характеристики предмета (содержимое характеристики)

POST '/thing-attribute/{thing-code}/{attribute-code}' create

PUT '/thing-attribute/{thing-code}/{attribute-code}' change
```
body MUST have value for content of the attribute of the thing :
content => value 
```

GET '/thing-attribute/essence-code/{essence-code}/filter/{filter}'
search
```
call parameters is :
essence-code => strict equality
filter MUST be like this :
search-parameters => 
[
    attribute-code =>[
        'values' => MUST be one of:
               [min-value,max-value] for continuous 
            or [value1, value2, .. , valueN] for discrete
    ]
] 

result MUST be collection of :
[ 
    thing-code
]
```
