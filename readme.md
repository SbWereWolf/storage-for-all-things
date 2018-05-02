# storage-for-all-things

"Хранилище для всего" - исследовательский проект для разработки движка "Универсального каталога"

# Услуги (варианты использования)

## Основные

- S001 Создать модель (предмет) с произвольными характеристиками
- S002 Выполнить поиск моделей по заданным характеристикам
- S003 Выполнить быстрый поиск
- S004 Выполнить быстрое обновление

## Дополниельные

- E005 Создать структуру для быстрого поиска
- E006 Обновить данные для быстрого поиска
- E007 Создать структуру для быстрого обновления

# Пример использования API для реализации Услуг

## S001 Создать модель (предмет) с произвольными характеристиками

## S001A1S01 создать сущность для предметов типа "пирожок"
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
POST '/attribute/price'
POST '/attribute/production-date'
POST '/attribute/place-of-production'
```
## S001A1S04 задать свойства характеристики
```
PUT '/attribute/price'
{
  "title": "цена, руб.",
  "data-type": "decimal",
  "range-type": "continuous"
}
PUT '/attribute/production-date'
{
  "title": "дата выработки",
  "data-type": "timestamp",
  "range-type": "continuous"
}
PUT '/attribute/place-of-production'
{
  "title": "Место производства",
  "data-type": "symbol",
  "range-type": "discrete"
}
```
## S001A1S05 охарактеризовать сущность (назначить типу хараектеристики для предметов этого типа)
```
POST '/essence-attribute/cake/price'
POST '/essence-attribute/cake/production-date'
POST '/essence-attribute/cake/place-of-production'
```

## S001A2S01 создать предметы типа "пирожок" (создать пирожки)
```
POST '/thing/cake/bun-with-jam'
POST '/thing/cake/bun-with-raisins'
POST '/thing/cake/cinnamon-bun'
```
## S001A2S02 задать свойства предметов (дать имена пирожкам)
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
## S001A2S03 задать значения для характеристики предмета
```
PUT '/thing-attribute/bun-with-jam/price'
{
  "value": 15.50
}
PUT '/thing-attribute/bun-with-jam/production-date'
{
  "value": "20180429T1356"
}
PUT '/thing-attribute/bun-with-jam/place-of-production'
{
  "value": "Екатеринбург"
}
PUT '/thing-attribute/bun-with-raisins/price'
{
  "value": 9.50
}
PUT '/thing-attribute/bun-with-raisins/production-date'
{
  "value": "20180427"
}
PUT '/thing-attribute/bun-with-raisins/place-of-production'
{
  "value": "Екатеринбург"
}
PUT '/thing-attribute/cinnamon-bun/price'
{
  "value": 4.50
}
PUT '/thing-attribute/cinnamon-bun/production-date'
{
  "value": "20180429"
}
PUT '/thing-attribute/cinnamon-bun/place-of-production'
{
  "value": "Челябинск"
}
```
## S001A4S04 получить данные из представления (без фильтрации)
```
GET '/thing-attribute/essence-code/cake/filter/'
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
## S002A4S03 определить возможные условия для поиска (параметры фильтрации)
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
GET '/thing-attribute/essence-code/cake/filter/price:4.5:10;production-date:20180427;20180429;place-of-production:Екатеринбург;Челябинск;'
result:
{
  "cake": [
    "bun-with-raisins",
    "cinnamon-bun"
  ]
}
```
