<?php
/* @var $actionLinks array */

use AllThings\Development\Page;

?>
<html>
<head>
    <meta charset="utf-8">
    <title>API call menu</title>
</head>

<body>

<div id="links">
    <dl>
        <dt>Common</dt>
        <dd><a href='<?= $actionLinks[Page::DEFAULT] ?>'>Меню вызовов API AllThings</a></dd>
    </dl>
    <dl>
        <dt>ESSENCE</dt>
        <dd><a href='<?= $actionLinks[Page::ADD_ESSENCE] ?>'>Добавить сущность</a></dd>
        <dd><a href='<?= $actionLinks[Page::VIEW_ESSENCE] ?>'>Показать сущность</a></dd>
        <dd><a href='<?= $actionLinks[Page::STORE_ESSENCE] ?>'>Записать свойства сущности</a></dd>
    </dl>
    <dl>
        <dt>ESSENCE-CATALOG</dt>
        <dd><a href='<?= $actionLinks[Page::VIEW_ESSENCE_CATALOG] ?>'>Показать весь каталог</a></dd>
        <dd><a href='<?= $actionLinks[Page::FILTER_ESSENCE_CATALOG] ?>'>Сделать выборку из каталога</a></dd>
    </dl>
    <dl>
        <dt>KIND</dt>
        <dd><a href='<?= $actionLinks[Page::ADD_KIND] ?>'>Добавить характеристику</a></dd>
        <dd><a href='<?= $actionLinks[Page::VIEW_KIND] ?>'>Показать характеристику</a></dd>
        <dd><a href='<?= $actionLinks[Page::STORE_KIND] ?>'>Записать свойства характеристики</a></dd>
    </dl>
    <dl>
        <dt>KIND-CATALOG</dt>
        <dd><a href='<?= $actionLinks[Page::VIEW_KIND_CATALOG] ?>'>Показать весь каталог</a></dd>
        <dd><a href='<?= $actionLinks[Page::FILTER_KIND_CATALOG] ?>'>Сделать выборку из каталога</a></dd>
    </dl>
    <dl>
        <dt>ESSENCE-KIND-LINK</dt>
        <dd><a href='<?= $actionLinks[Page::ADD_ESSENCE_KIND_LINK] ?>'>Добавить характеристику к сущности</a></dd>
        <dd><a href='<?= $actionLinks[Page::REMOVE_ESSENCE_KIND_LINK] ?>'>Удалить характеристику у сущности</a></dd>
        <dd><a href='<?= $actionLinks[Page::VIEW_KIND_OF_ESSENCE] ?>'>Показать все характеристики сущности</a></dd>
    </dl>
    <dl>
        <dt>THING</dt>
        <dd><a href='<?= $actionLinks[Page::ADD_THING] ?>'>Добавить экземпляр</a></dd>
        <dd><a href='<?= $actionLinks[Page::VIEW_THING] ?>'>Показать экземпляр</a></dd>
        <dd><a href='<?= $actionLinks[Page::STORE_THING] ?>'>Записать свойства экземпляра</a></dd>
    </dl>
    <dl>
        <dt>FILTER-ESSENCE</dt>
        <dd><a href='<?= $actionLinks[Page::FILTER_OF_ESSENCE] ?>'>Показать возможные условия выборки по сущности</a></dd>
    </dl>
    <dl>
        <dt>KIND-OF-THING</dt>
        <dd><a href='<?= $actionLinks[Page::ADD_KIND_TO_THING] ?>'>Добавить характеристику к экземпляру</a></dd>
        <dd><a href='<?= $actionLinks[Page::STORE_KIND_OF_THING] ?>'>Записать значение характеристики у
                экземпляра</a></dd>
        <dd><a href='<?= $actionLinks[Page::FILTER_THING_BY_KIND] ?>'>Сделать выборку из экземпляров сущности</a></dd>
    </dl>

</div>
</body>
</html>
