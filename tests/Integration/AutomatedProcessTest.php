<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:46
 */

namespace Integration;

use AllThings\ControlPanel\Browser;
use AllThings\ControlPanel\Operator;
use AllThings\ControlPanel\Schema;
use AllThings\DataAccess\Crossover\Crossover;
use AllThings\SearchEngine\ContinuousFilter;
use AllThings\SearchEngine\DiscreteFilter;
use AllThings\StorageEngine\Storable;
use Environment\Database\DbConnection;
use Exception;
use PHPUnit\Framework\TestCase;

class AutomatedProcessTest extends TestCase
{
    public const SKIP = false;

    /**
     * Настраиваем тестовое окружение (соединение с БД)
     * @return array
     */
    public function testInit(): array
    {
        $linkToData = (new DbConnection())->getForRead();

        $isSuccess = static::SKIP;
        if (!static::SKIP) {
            $isSuccess = $linkToData->beginTransaction();
        }
        $this->assertTrue($isSuccess, 'Transaction must be opened');

        $context = [];
        $context['PDO'] = $linkToData;

        return $context;
    }

    /**
     * Создаём сущность - пирожки
     * @depends testInit
     *
     * @param array $context
     *
     * @return array
     * @throws Exception
     */
    public function testBlueprintCreate(array $context): array
    {
        $linkToData = $context['PDO'];

        $operator = new Operator($linkToData);

        /* ## S001A1S01 создать сущность для предметов типа "пирожок" */
        $essence = $operator->createBlueprint(
            'cake',
            Storable::DIRECT_READING,
            'The Cakes',
            'Cakes  of all kinds'
        );
        $this->assertNotEmpty(
            $essence,
            'Essence must be created with success'
        );

        $context['essence'] = 'cake';

        return $context;
    }

    /**
     * Создаём характеристики
     * @depends testBlueprintCreate
     *
     * @param array $context
     *
     * @return array
     * @throws Exception
     */
    public function testKindCreate(array $context): array
    {
        /* ## S001A1S03 создать характеристику */
        /* ## S001A1S04 задать свойства характеристики */

        $codes = [
            'price' => [
                'Title' => 'цена, руб.',
                'DataType' => 'decimal',
                'RangeType' => 'continuous',
            ],
            'production-date' => [
                'Title' => 'дата выработки',
                'DataType' => 'timestamp',
                'RangeType' => 'continuous',
            ],
            'place-of-production' => [
                'Title' => 'Место производства',
                'DataType' => 'symbols',
                'RangeType' => 'discrete',
            ],
        ];

        $operator = new Operator($context['PDO']);
        foreach ($codes as $code => $settings) {
            $attribute = $operator->createKind(
                $code,
                $settings['DataType'],
                $settings['RangeType'],
                $settings['Title'],
            );

            $this->assertNotEmpty(
                $attribute,
                'Attribute must be created with success'
            );
            $context[$code] = $code;
        }

        return $context;
    }

    /**
     * Задаём характеристики для сущности
     * @depends testKindCreate
     *
     * @param array $context
     *
     * @return array
     * @throws Exception
     */
    public function testDefineBlueprint(array $context): array
    {
        /* ## S001A1S05 охарактеризовать сущность (назначить
         характеристики для предметов этого типа) */
        $essence = $context['essence'];

        $attributes = ['price', 'production-date', 'place-of-production'];
        $operator = new Operator($context['PDO']);
        foreach ($attributes as $attribute) {
            $operator->attachKind(
                $essence,
                $attribute,
            );
        }

        $this->assertTrue(
            true,
            'Blueprint must be defined with success'
        );

        return $context;
    }

    /** Создаём модели на основе сущности
     * @depends testDefineBlueprint
     *
     * @param array $context
     *
     * @return array
     * @throws Exception
     */
    public function testCreateItem(array $context): array
    {
        /* ## S001A2S01 создать предметы типа "пирожок"
        (создать пирожки) */
        /* ## S001A2S02 задать значения свойствам предметов
        (дать имена пирожкам) */
        $titles = [];
        $titles['bun-with-jam'] = 'Булочка с повидлом';
        $titles['bun-with-raisins'] = 'Булочка с изюмом';
        $titles['cinnamon-bun'] = 'Булочка с корицей';

        $operator = new Operator($context['PDO']);
        foreach ($titles as $code => $title) {
            $context[$code] = $code;

            $operator->createItem(
                $context['essence'],
                $code,
                $title,
            );
        }

        $this->assertTrue(true, "Thing must be created with success");

        return $context;
    }

    /**
     * Задаём значения характеристикам моделей
     * @depends testCreateItem
     *
     * @param array $context
     *
     * @return array
     * @throws Exception
     */
    public function testCreateContent(array $context): array
    {
        /* ## S001A2S03 задать значения для характеристики предмета */
        $linkToData = $context['PDO'];

        $codes = [
            /* модель */
            $context['bun-with-jam'] => [
                /* характеристика и её значение */
                $context['price'] => '15.50',
                $context['production-date'] => '20180429T1356',
                $context['place-of-production'] => 'Екатеринбург',
            ],
            $context['bun-with-raisins'] => [
                $context['price'] => '9.50',
                $context['production-date'] => '20180427',
                $context['place-of-production'] => 'Екатеринбург',
            ],
            $context['cinnamon-bun'] => [
                $context['price'] => '4.50',
                $context['production-date'] => '20180429',
                $context['place-of-production'] => 'Челябинск',
            ],
        ];

        $operator = new Operator($linkToData);
        foreach ($codes as $code => $settings) {
            foreach ($settings as $attribute => $value) {
                $operator->changeContent(
                    $code,
                    $attribute,
                    $value,
                );
            }
        }
        $this->assertTrue(true, 'Content must be created with success');

        return $context;
    }

    /**
     * Создаём представление для характеристик моделей
     * @depends testDefineBlueprint
     *
     * @param array $context
     * @throws Exception
     */
    public function testCreateView(array $context)
    {
        /* S001A4S02 создать представление */
        $schema = new Schema($context['PDO'], $context['essence']);
        $schema->handleWithDirectReading();

        $this->assertTrue(true, 'View must be created with success');
    }

    /**
     * Получаем данные всех моделей из представления
     * @depends testCreateContent
     *
     * @param array $context
     * @throws Exception
     */
    public function testShowAllFromView(array $context)
    {
        /* ## S001A4S04 получить данные из представления
        (без фильтрации) */
        $browser = new Browser($context['PDO']);
        $data = $browser->filterData($context['essence'], []);
        $this->checkShowAll($context, $data);
    }

    /**
     * @param array $context
     * @param array $data
     * @param bool $withAdditional
     * @param bool $withExtended
     * @param bool $withChanges
     */
    private function checkShowAll(
        array $context,
        array $data,
        bool $withAdditional = false,
        bool $withExtended = false,
        bool $withChanges = false
    ): void {
        $essence = $context['essence'];
        $properNumbers = 3;
        if ($withAdditional) {
            $properNumbers = 4;
        }
        $isEnough = (count($data) === $properNumbers && !$withAdditional)
            || (count($data) === $properNumbers && $withAdditional);
        $this->assertTrue(
            $isEnough,
            "Essence `$essence` must have ($properNumbers) things"
        );

        $thingTested = 0;
        foreach ($data as $thing) {
            $code = $thing['code'];
            switch ($code) {
                case $context['bun-with-jam']:
                    $isProper = true;
                    $isProper = $isProper
                        && $thing[$context['price']] === '15.50';
                    $isProper = $isProper
                        && $thing[$context['production-date']]
                        === '20180429T1356';
                    $isProper = $isProper
                        && $thing[$context['place-of-production']]
                        === 'Екатеринбург';

                    if ($isProper && $withExtended) {
                        $isProper = $thing[$context['package']]
                            === 'без упаковки';
                    }

                    $this->assertTrue(
                        $isProper,
                        "Thing `$code`"
                        . ' must have same content as defined'
                    );
                    $thingTested++;
                    break;
                case $context['bun-with-raisins']:
                    $isProper = true;
                    $isProper = $isProper
                        && $thing[$context['price']] === '9.50';
                    $isProper = $isProper
                        && $thing[$context['production-date']]
                        === '20180427';
                    $isProper = $isProper
                        && $thing[$context['place-of-production']]
                        === 'Екатеринбург';

                    if ($isProper && $withExtended) {
                        $isProper = $thing[$context['package']]
                            === 'без упаковки';
                    }

                    $this->assertTrue(
                        $isProper,
                        "Thing `$code`"
                        . ' must have same content as defined'
                    );
                    $thingTested++;
                    break;
                case $context['cinnamon-bun']:
                    $isProper = true;
                    $isProper = $isProper
                        && $thing[$context['price']] === '4.50';
                    $isProper = $isProper
                        && $thing[$context['production-date']]
                        === '20180429';
                    $isProper = $isProper
                        && $thing[$context['place-of-production']]
                        === 'Челябинск';

                    if ($isProper && $withExtended) {
                        $isProper = $thing[$context['package']]
                            === 'пакет';
                    }

                    $this->assertTrue(
                        $isProper,
                        "Thing `$code`"
                        . ' must have same content as defined'
                    );
                    $thingTested++;
                    break;
                case $context['new-thing']:
                    $isProper = true;
                    $isProper = $isProper
                        && $thing[$context['price']] === '11.11';
                    $isProper = $isProper
                        && $thing[$context['production-date']]
                        === '20210531T0306';
                    $isProper = $isProper
                        && $thing[$context['place-of-production']]
                        === 'Екатеринбург';

                    if ($isProper && $withExtended && !$withChanges) {
                        $isProper = $thing[$context['package']]
                            === 'пакет';
                    }
                    if ($isProper && $withChanges) {
                        $isProper = $thing[$context['package']]
                            === 'коробка';
                    }

                    $this->assertTrue(
                        $isProper,
                        "Thing `$code`"
                        . ' must have same content as defined'
                    );
                    $thingTested++;
                    break;
            }
        }

        $isEnough = ($thingTested === 3 && !$withAdditional)
            || ($thingTested === 4 && $withAdditional);
        $this->assertTrue(
            $isEnough,
            "Each thing of essence `$essence`"
            . ' must be tested for matching with defined'
        );
    }

    /**
     * Получаем значения фильтров для поиска моделей в представлении
     * по значениям характеристик
     * @depends testCreateContent
     *
     * @param array $context
     * @throws Exception
     */
    public function testGetFiltersForView(array $context)
    {
        /* ## S002A4S03 определить возможные условия для поиска
        (параметры фильтрации) */
        $browser = new Browser($context['PDO']);
        $filters = $browser->filters($context['essence']);

        $this->checkFilters($filters, $context['essence']);
    }

    /**
     * @param array $data
     * @param string $essence
     */
    private function checkFilters(
        array $data,
        string $essence
    ): void {
        $this->assertTrue(
            count($data) === 2,
            "Filters of essence `$essence`"
            . ' must have two types'
        );
        $this->assertArrayHasKey(
            'continuous', $data,
            "Filters of essence `$essence`"
            . ' must have type continuous'
        );
        $this->assertArrayHasKey(
            'discrete', $data,
            "Filters of essence `$essence`"
            . ' must have type discrete'
        );

        $filtersValue = 'a:2:{s:10:"continuous";a:4:{s:9:"max@price";'
            . 's:4:"9.50";s:9:"min@price";s:5:"15.50";'
            . 's:19:"max@production-date";s:13:"20180429T1356";'
            . 's:19:"min@production-date";s:8:"20180427";}'
            . 's:8:"discrete";a:1:{s:19:"place-of-production";'
            . 'a:2:{i:0;s:24:"Екатеринбург";i:1;s:18:"Челябинск";}}}';
        $this->assertTrue(
            serialize($data) === $filtersValue,
            "Filters of essence `$essence` must have proper value"
        );
    }

    /**
     * Фильтруем модели из представления по заданным значениям
     * характеристик
     * @depends testCreateContent
     *
     * @param array $context
     * @throws Exception
     */
    public function testSearchWithinView(array $context)
    {
        /* ## ## S002A4S04 сделать выборку экземпляров по заданным
        условиям поиска (поиск в представлении) */
        $continuous = new ContinuousFilter(
            $context['price'], '15.50', '4.50'
        );
        $discrete = new DiscreteFilter(
            $context['place-of-production'], ['Челябинск']
        );
        $browser = new Browser($context['PDO']);

        $data = $browser->filterData($context['essence'], [$continuous]);
        $this->assertTrue(!empty($data));

        $data = $browser->filterData($context['essence'], [$discrete]);
        $this->assertTrue(!empty($data));

        $data = $browser->filterData(
            $context['essence'],
            [$discrete, $continuous]
        );
        $this->assertTrue(!empty($data));
    }

    /**
     * Создаём материализованное представление для характеристик
     * моделей
     * @depends testDefineBlueprint
     *
     * @param array $context
     * @throws Exception
     */
    public function testCreateMathView(array $context)
    {
        /* S001A4S02 создать материализованное представление */
        $schema = new Schema($context['PDO'], $context['essence']);
        $schema->handleWithRapidObtainment();

        $this->assertTrue(true, 'Math view must be created with success');
    }

    /**
     * Читаем характеристики моделей из материализованного
     * представления
     * @depends testCreateContent
     *
     * @param array $context
     * @throws Exception
     */
    public function testShowAllFromMathView(array $context)
    {
        /* ## S001A4S04 получить данные из представления
        (без фильтрации) */
        $browser = new Browser($context['PDO']);
        $data = $browser->filterData($context['essence'], []);
        $this->checkShowAll($context, $data);
    }

    /**
     * Получаем значения фильтров для поиска моделей в
     * материализованном представлении по значениям характеристик
     * @depends testCreateContent
     *
     * @param array $context
     * @throws Exception
     */
    public function testGetFiltersForMathView(array $context)
    {
        /* ## S002A4S03 определить возможные условия для поиска
        (параметры фильтрации) */
        $browser = new Browser($context['PDO']);
        $filters = $browser->filters($context['essence']);

        $this->checkFilters($filters, $context['essence']);
    }

    /**
     * Фильтруем модели из материализованного представления по
     * заданным значениям характеристик
     * @depends testCreateContent
     *
     * @param array $context
     * @throws Exception
     */
    public function testSearchWithinMathView(array $context)
    {
        /* ## ## S002A4S04 сделать выборку экземпляров по заданным
        условиям поиска (поиск в представлении) */
        $continuous = new ContinuousFilter(
            $context['price'], '15.50', '4.50'
        );
        $discrete = new DiscreteFilter(
            $context['place-of-production'], ['Челябинск']
        );
        $browser = new Browser($context['PDO']);

        $data = $browser->filterData($context['essence'], [$continuous]);
        $this->assertTrue(!empty($data));

        $data = $browser->filterData($context['essence'], [$discrete]);
        $this->assertTrue(!empty($data));

        $data = $browser->filterData(
            $context['essence'],
            [$discrete, $continuous]
        );
        $this->assertTrue(!empty($data));
    }

    /**
     * Создаём таблицу для значений характеристик моделей
     * @depends testDefineBlueprint
     *
     * @param array $context
     * @throws Exception
     */
    public function testCreateTable(array $context)
    {
        /* S001A4S02 создать представление */
        $schema = new Schema($context['PDO'], $context['essence']);
        $schema->handleWithRapidRecording();

        $this->assertTrue(true, 'Table must be created with success');
    }

    /**
     * Получаем характеристики всех моделей из таблицы
     * @depends testCreateContent
     *
     * @param array $context
     * @throws Exception
     */
    public function testShowAllFromTable(array $context)
    {
        /* ## S001A4S04 получить данные из представления
        (без фильтрации) */
        $browser = new Browser($context['PDO']);
        $data = $browser->filterData($context['essence'], []);
        $this->checkShowAll($context, $data);
    }

    /**
     * Получаем значения фильтров для поиска моделей в таблице
     * по значениям характеристик
     * @depends testCreateContent
     *
     * @param array $context
     * @throws Exception
     */
    public function testGetFiltersForTable(array $context)
    {
        /* ## S002A4S03 определить возможные условия для поиска
        (параметры фильтрации) */
        $browser = new Browser($context['PDO']);
        $filters = $browser->filters($context['essence']);

        $this->checkFilters($filters, $context['essence']);
    }

    /**
     * Фильтруем модели из таблицы по заданным значениям
     * характеристик
     * @depends testCreateContent
     *
     * @param array $context
     * @throws Exception
     */
    public function testSearchWithinTable(array $context)
    {
        /* ## ## S002A4S04 сделать выборку экземпляров по заданным
        условиям поиска (поиск в представлении) */
        $continuous = new ContinuousFilter(
            $context['price'], '15.50', '4.50'
        );
        $discrete = new DiscreteFilter(
            $context['place-of-production'], ['Челябинск']
        );
        $browser = new Browser($context['PDO']);

        $data = $browser->filterData($context['essence'], [$continuous]);
        $this->assertTrue(!empty($data));

        $data = $browser->filterData($context['essence'], [$discrete]);
        $this->assertTrue(!empty($data));

        $data = $browser->filterData(
            $context['essence'],
            [$discrete, $continuous]
        );
        $this->assertTrue(!empty($data));
    }

    /**
     * Добавляем новую модель
     * @depends testCreateItem
     *
     * @param array $context
     * @return array
     * @throws Exception
     */
    public function testAddNewItem(array $context): array
    {
        $linkToData = $context['PDO'];
        $context['new-thing'] = 'new-thing';
        /* добавляем модель, задаём для неё атрибуты */
        /* даём модели название */
        $operator = new Operator($linkToData);
        $operator->createItem(
            $context['essence'],
            $context['new-thing'],
            'новая модель',
        );

        /* задаём характеристики модели */
        $operator->changeContent(
            $context['new-thing'],
            $context['price'],
            '11.11',
        );
        $operator->changeContent(
            $context['new-thing'],
            $context['production-date'],
            '20210531T0306',
        );
        $operator->changeContent(
            $context['new-thing'],
            $context['place-of-production'],
            'Екатеринбург',
        );
        $this->assertTrue(true, 'Item must be created with success');

        return $context;
    }

    /**
     * Добавляем новую модель в представление
     * @depends testAddNewItem
     *
     * @param array $context
     * @throws Exception
     */
    public function testAddNewItemToView(array $context)
    {
        $schema = new Schema($context['PDO'], $context['essence']);
        $schema->changeStorage(Storable::DIRECT_READING);
        $schema->refresh();

        $browser = new Browser($context['PDO']);
        $data = $browser->filterData($context['essence'], []);
        $this->checkShowAll($context, $data, true);
    }

    /**
     * Добавляем новую модель в материализованное представление
     * @depends testAddNewItem
     *
     * @param array $context
     * @throws Exception
     */
    public function testAddNewItemToMathView(array $context)
    {
        $schema = new Schema($context['PDO'], $context['essence']);
        $schema->changeStorage(Storable::RAPID_OBTAINMENT);
        $schema->refresh();

        $browser = new Browser($context['PDO']);
        $data = $browser->filterData($context['essence'], []);
        $this->checkShowAll($context, $data, true);
    }

    /**
     * Добавляем новую модель в таблицу
     * @depends testAddNewItem
     *
     * @param array $context
     * @throws Exception
     */
    public function testAddNewItemToTable(array $context)
    {
        $schema = new Schema($context['PDO'], $context['essence']);
        $schema->changeStorage(Storable::RAPID_RECORDING);
        $schema->refresh();

        $browser = new Browser($context['PDO']);
        $data = $browser->filterData($context['essence'], []);
        $this->checkShowAll($context, $data, true);
    }

    /**
     * Добавляем новую характеристику
     * @depends testAddNewItem
     *
     * @param array $context
     *
     * @return array
     * @throws Exception
     */
    public function testAddNewKind(array $context): array
    {
        /* Добавляем новую характеристику package и задаём параметры
        этой характеристики */

        $codes = [
            'package' => [
                'Title' => 'Упаковка',
                'DataType' => 'symbols',
                'RangeType' => 'discrete',
            ],
        ];

        $linkToData = $context['PDO'];
        $operator = new Operator($linkToData);
        foreach ($codes as $code => $settings) {
            $attribute = $operator->createKind(
                $code,
                $settings['DataType'],
                $settings['RangeType'],
                $settings['Title'],
            );

            $this->assertNotEmpty(
                $attribute,
                'Attribute must be created with success'
            );
            $context[$code] = $code;
        }

        /* Добавим сущности cake новую характеристику package */
        $essence = $context['essence'];
        $operator->attachKind(
            $essence,
            $code,
        );

        /* Добавим существующим моделям новую характеристику */
        /* Зададим значения новой характеристики для всех моделей */
        $thingList = [
            'bun-with-jam' => 'без упаковки',
            'bun-with-raisins' => 'без упаковки',
            'cinnamon-bun' => 'пакет',
            'new-thing' => 'пакет',
        ];
        foreach ($thingList as $thing => $value) {
            $context[$thing] = $thing;
            $operator->expandItem($thing, $code, $value);
        }

        return $context;
    }

    /**
     * Добавляем новую характеристику в представление
     * @depends testAddNewKind
     *
     * @param array $context
     * @throws Exception
     */
    public function testAddNewKindToView(array $context)
    {
        $schema = new Schema($context['PDO'], $context['essence']);
        $schema->changeStorage(Storable::DIRECT_READING);
        $schema->setup();

        $browser = new Browser($context['PDO']);
        $data = $browser->filterData($context['essence'], []);
        $this->checkShowAll($context, $data, true);
    }

    /**
     * Добавляем новую характеристику в материализованное
     * представление
     * @depends testAddNewKind
     *
     * @param array $context
     * @throws Exception
     */
    public function testAddNewKindToMathView(array $context)
    {
        $schema = new Schema($context['PDO'], $context['essence']);
        $schema->changeStorage(Storable::RAPID_OBTAINMENT);
        $schema->setup();

        $browser = new Browser($context['PDO']);
        $data = $browser->filterData($context['essence'], []);
        $this->checkShowAll($context, $data, true);
    }

    /**
     * Добавляем новую характеристику в таблицу
     * @depends testAddNewKind
     *
     * @param array $context
     * @throws Exception
     */
    public function testAddNewKindToTable(array $context)
    {
        $schema = new Schema($context['PDO'], $context['essence']);
        $schema->changeStorage(Storable::RAPID_RECORDING);
        $schema->setup();

        $browser = new Browser($context['PDO']);
        $data = $browser->filterData($context['essence'], []);
        $this->checkShowAll($context, $data, true);
    }

    /**
     * Изменим значение характеристики модели
     * @depends testAddNewKind
     *
     * @param array $context
     *
     * @return array
     * @throws Exception
     */
    public function testChangeContent(array $context): array
    {
        $operator = new Operator($context['PDO']);
        $operator->changeContent(
            $context['new-thing'],
            $context['package'],
            'коробка',
        );

        $this->assertTrue(true, 'Content'
            . ' must be changed with success');

        return $context;
    }

    /**
     * Добавляем новую модель в представление
     * @depends testAddNewKind
     *
     * @param array $context
     * @throws Exception
     */
    public function testChangeContentWithinView(array $context)
    {
        $schema = new Schema($context['PDO'], $context['essence']);
        $schema->changeStorage(Storable::DIRECT_READING);
        $schema->refresh();

        $browser = new Browser($context['PDO']);
        $data = $browser->filterData($context['essence'], []);
        $this->checkShowAll(
            $context, $data,
            true,
            true,
            true
        );
    }

    /**
     * Добавляем новую модель в материализованное представление
     * @depends testAddNewKind
     *
     * @param array $context
     * @throws Exception
     */
    public function testChangeContentWithinMathView(array $context)
    {
        $schema = new Schema($context['PDO'], $context['essence']);
        $schema->changeStorage(Storable::RAPID_OBTAINMENT);
        $schema->refresh();

        $browser = new Browser($context['PDO']);
        $data = $browser->filterData($context['essence'], []);
        $this->checkShowAll(
            $context, $data,
            true,
            true,
            true
        );
    }

    /**
     * Добавляем новую модель в таблицу
     * @depends testAddNewKind
     *
     * @param array $context
     * @throws Exception
     */
    public function testChangeContentWithinTable(array $context)
    {
        $schema = new Schema($context['PDO'], $context['essence']);
        $schema->changeStorage(Storable::RAPID_RECORDING);

        $content = (new Crossover())->
        setLeftValue($context['new-thing'])
            ->setRightValue($context['package'])
            ->setContent('коробка');
        $schema->refresh($content);

        $browser = new Browser($context['PDO']);
        $data = $browser->filterData($context['essence'], []);
        $this->checkShowAll(
            $context, $data,
            true,
            true,
            true
        );
    }

    /**
     * Заключительные действия, откатываем транзакцию
     * @depends testInit
     *
     * @param array $context
     */
    public function testFinally(array $context)
    {
        $isSuccess = static::SKIP;
        if (!static::SKIP) {
            $linkToData = $context['PDO'];
            $isSuccess = $linkToData->rollBack();
        }
        $this->assertTrue(
            $isSuccess,
            'Transaction must be rolled back'
        );
    }
}
