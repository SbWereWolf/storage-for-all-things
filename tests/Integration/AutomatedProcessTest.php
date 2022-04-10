<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 4/10/22, 3:10 PM
 */

/*
 * !! WARNING !!
 * Option 'timezone' of postgresql.conf MUST BE 'UTC':
 * timezone = 'UTC'
 * */

namespace Integration;

use AllThings\ControlPanel\Browser;
use AllThings\ControlPanel\Designer;
use AllThings\ControlPanel\Operator;
use AllThings\ControlPanel\ProductionLine;
use AllThings\ControlPanel\Redactor;
use AllThings\SearchEngine\ContinuousFilter;
use AllThings\SearchEngine\DiscreteFilter;
use AllThings\StorageEngine\Storable;
use AllThings\StorageEngine\StorageManager;
use Environment\Database\PdoConnection;
use Exception;
use PHPUnit\Framework\TestCase;

class AutomatedProcessTest extends TestCase
{
    public const USE_TRANSACTION = true;

    /**
     * Настраиваем тестовое окружение (соединение с БД)
     * @return array
     */
    public function testInit(): array
    {
        $pathParts = [
            __DIR__,
            '..',
            '..',
            'configuration',
            'pdo.env',
        ];
        $path = implode(DIRECTORY_SEPARATOR, $pathParts);
        $linkToData = (new PdoConnection($path))->get();

        $isSuccess = true;
        if (static::USE_TRANSACTION) {
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

        /* ## S001A1S01 создать сущность для предметов типа "пирожок" */
        $designer = new Designer($linkToData);
        $essence = $designer->essence(
            'cake',
            'The Cakes',
            'Cakes of all kinds',
        );
        $this->assertNotEmpty(
            $essence,
            'Essence must be created with success'
        );

        $context['essence'] = $essence->getCode();

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
        $linkToData = $context['PDO'];
        /* ## S001A1S03 создать характеристику */
        /* ## S001A1S04 задать свойства характеристики */

        $codes = [
            'price' => [
                'Title' => 'цена, руб.',
                'DataType' => 'number',
                'RangeType' => 'continuous',
            ],
            'production-date' => [
                'Title' => 'дата выработки',
                'DataType' => 'time',
                'RangeType' => 'continuous',
            ],
            'place-of-production' => [
                'Title' => 'Место производства',
                'DataType' => 'word',
                'RangeType' => 'discrete',
            ],
        ];

        foreach ($codes as $code => $settings) {
            $designer = new Designer($linkToData);
            $attribute = $designer->attribute(
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
     * Формируем категорию из атрибутов
     *
     * @depends testKindCreate
     *
     * @param array $context
     *
     * @return array
     * @throws Exception
     */
    public function testDefineBlueprint(array $context): array
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];
        $attributes = [
            'price',
            'production-date',
            'place-of-production'
        ];
        /* ## S001A1S05 охарактеризовать сущность (назначить
         характеристики для предметов этого типа) */

        $manager = new Redactor($linkToData, $essence);
        $manager->expand($attributes);

        $this->assertTrue(true);

        return $context;
    }

    /** Создаём модели на основе категории, формируем каталог
     *
     * @depends testDefineBlueprint
     *
     * @param array $context
     *
     * @return array
     * @throws Exception
     */
    public function testCreateItem(array $context): array
    {
        $linkToData = $context['PDO'];
        $titles = [];
        $titles['bun-with-jam'] = 'Булочка с повидлом';
        $titles['bun-with-raisins'] = 'Булочка с изюмом';
        $titles['cinnamon-bun'] = 'Булочка с корицей';

        /* ## S001A2S01 создать предметы типа "пирожок"
        (создать пирожки) */
        /* ## S001A2S02 задать значения свойствам предметов
        (дать имена пирожкам) */

        $designer = new Designer($linkToData);
        foreach ($titles as $code => $title) {
            $context[$code] = $code;

            $product = $designer->product($code, $title);
        }

        $this->assertNotEmpty(
            $product,
            'Product must be created with success'
        );

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
        $codes = [
            /* элемент каталога */
            $context['bun-with-jam'] => [
                /* характеристика и её значение */
                $context['price'] => '15.50',
                $context['production-date'] => '20180429T1356+00:00',
                $context['place-of-production'] => 'Екатеринбург',
            ],
            $context['bun-with-raisins'] => [
                $context['price'] => '9.50',
                $context['production-date'] => '20180427T00:00+00:00',
                $context['place-of-production'] => 'Екатеринбург',
            ],
            $context['cinnamon-bun'] => [
                $context['price'] => '4.50',
                $context['production-date'] => '20180429T00:00+00:00',
                $context['place-of-production'] => 'Челябинск',
            ],
        ];
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        /* ## S001A2S03 задать значения для характеристики предмета */
        foreach ($codes as $code => $settings) {
            (new ProductionLine($linkToData, $code))
                ->setup($essence, $settings);
        }
        $this->assertTrue(
            true,
            'Content must be created with success'
        );

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
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->handleWithDirectReading();

        $this->assertTrue(
            true,
            'View must be created with success'
        );
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
        $data = $browser->find($context['essence'], []);

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

        $productTested = 0;
        foreach ($data as $product) {
            $code = $product['code'];
            switch ($code) {
                case $context['bun-with-jam']:
                    $isProper = true;
                    /** @noinspection PhpConditionAlreadyCheckedInspection */
                    $isProper = $isProper
                        && $product[$context['price']] === '15.5000';
                    $isProper = $isProper
                        && $product[$context['production-date']]
                        === '2018-04-29 13:56:00+00';
                    $isProper = $isProper
                        && $product[$context['place-of-production']]
                        === 'Екатеринбург';

                    if ($isProper && !$withExtended) {
                        $isProper = !key_exists(
                            $context['package'] ?? '',
                            $product,
                        );
                    }

                    if ($isProper && $withExtended) {
                        $isProper = $product[$context['package']]
                            === 'без упаковки';
                    }

                    $this->assertTrue(
                        $isProper,
                        "Thing `$code`"
                        . ' must have same content as defined'
                    );
                    $productTested++;
                    break;
                case $context['bun-with-raisins']:
                    $isProper = true;
                    /** @noinspection PhpConditionAlreadyCheckedInspection */
                    $isProper = $isProper
                        && $product[$context['price']] === '9.5000';
                    $isProper = $isProper
                        && $product[$context['production-date']]
                        === '2018-04-27 00:00:00+00';
                    $isProper = $isProper
                        && $product[$context['place-of-production']]
                        === 'Екатеринбург';

                    if ($isProper && !$withExtended) {
                        $isProper = !key_exists(
                            $context['package'] ?? '',
                            $product,
                        );
                    }

                    if ($isProper && $withExtended) {
                        $isProper = $product[$context['package']]
                            === 'без упаковки';
                    }

                    $this->assertTrue(
                        $isProper,
                        "Thing `$code`"
                        . ' must have same content as defined'
                    );
                    $productTested++;
                    break;
                case $context['cinnamon-bun']:
                    $isProper = true;
                    /** @noinspection PhpConditionAlreadyCheckedInspection */
                    $isProper = $isProper
                        && $product[$context['price']] === '4.5000';
                    $isProper = $isProper
                        && $product[$context['production-date']]
                        === '2018-04-29 00:00:00+00';
                    $isProper = $isProper
                        && $product[$context['place-of-production']]
                        === 'Челябинск';

                    if ($isProper && !$withExtended) {
                        $isProper = !key_exists(
                            $context['package'] ?? '',
                            $product,
                        );
                    }

                    if ($isProper && $withExtended) {
                        $isProper = $product[$context['package']]
                            === 'пакет';
                    }

                    $this->assertTrue(
                        $isProper,
                        "Thing `$code`"
                        . ' must have same content as defined'
                    );
                    $productTested++;
                    break;
                case $context['new-thing']:
                    $isProper = true;
                    /** @noinspection PhpConditionAlreadyCheckedInspection */
                    $isProper = $isProper
                        && $product[$context['price']] === '11.1100';
                    $isProper = $isProper
                        && $product[$context['production-date']]
                        === '2021-05-31 03:06:00+00';
                    $isProper = $isProper
                        && $product[$context['place-of-production']]
                        === 'Екатеринбург';

                    if ($isProper && $withExtended && !$withChanges) {
                        $isProper = $product[$context['package']]
                            === 'пакет';
                    }
                    if ($isProper && $withChanges) {
                        $isProper = $product[$context['package']]
                            === 'коробка';
                    }

                    $this->assertTrue(
                        $isProper,
                        "Thing `$code`"
                        . ' must have same content as defined'
                    );
                    $productTested++;
                    break;
            }
        }

        $isEnough = ($productTested === 3 && !$withAdditional)
            || ($productTested === 4 && $withAdditional);
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
        $filtersValue = 'a:3:{i:0;O:37:"AllThings\SearchEngine\Disc' .
            'reteFilter":3:{s:40:" AllThings\SearchEngine\Filter' .
            ' attribute";s:19:"place-of-production";s:39:" AllT' .
            'hings\SearchEngine\Filter dataType";s:4:"word";s:45:' .
            '" AllThings\SearchEngine\DiscreteFilter values";a:' .
            '2:{i:0;s:24:"Екатеринбург";i:1;s:18:"Челябинск";}}i:1;' .
            'O:39:"AllThings\SearchEngine\ContinuousFilter":4:{s:40' .
            ':" AllThings\SearchEngine\Filter attribute";s:5:"p' .
            'rice";s:39:" AllThings\SearchEngine\Filter dataTyp' .
            'e";s:6:"number";s:44:" AllThings\SearchEngine\Contin' .
            'uousFilter min";s:6:"4.5000";s:44:" AllThings\Sear' .
            'chEngine\ContinuousFilter max";s:7:"15.5000";}i:2;O:' .
            '39:"AllThings\SearchEngine\ContinuousFilter":4:{s:40:"' .
            ' AllThings\SearchEngine\Filter attribute";s:15:"pr' .
            'oduction-date";s:39:" AllThings\SearchEngine\Filter' .
            ' dataType";s:4:"time";s:44:" AllThings\SearchEngin' .
            'e\ContinuousFilter min";s:22:"2018-04-27 00:00:00+00' .
            '";s:44:" AllThings\SearchEngine\ContinuousFilter m' .
            'ax";s:22:"2018-04-29 13:56:00+00";}}';
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
            $context['price'], 'number', '4.50', '9.50'
        );
        $discrete = new DiscreteFilter(
            $context['place-of-production'], 'word', ['Екатеринбург']
        );
        $browser = new Browser($context['PDO']);

        $data = $browser->find($context['essence'], [$continuous]);
        $this->assertNotEmpty($data);

        $data = $browser->find($context['essence'], [$discrete]);
        $this->assertNotEmpty($data);

        $data = $browser->find(
            $context['essence'],
            [$discrete, $continuous]
        );
        $this->assertNotEmpty($data);
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
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->handleWithRapidObtainment();

        $this->assertTrue(
            true,
            'Math view must be created with success'
        );
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
        $data = $browser->find($context['essence'], []);
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
            $context['price'], 'number', '4.50', '9.50'
        );
        $discrete = new DiscreteFilter(
            $context['place-of-production'], 'word', ['Екатеринбург']
        );
        $browser = new Browser($context['PDO']);

        $data = $browser->find($context['essence'], [$continuous]);
        $this->assertNotEmpty($data);

        $data = $browser->find($context['essence'], [$discrete]);
        $this->assertNotEmpty($data);

        $data = $browser->find(
            $context['essence'],
            [$discrete, $continuous]
        );
        $this->assertNotEmpty($data);
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
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->handleWithRapidRecording();

        $this->assertTrue(
            true,
            'Table must be created with success'
        );
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
        $data = $browser->find($context['essence'], []);
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
        /* ## S002A4S03 определить границы поиска в характеристиках
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
        $essence = $context['essence'];
        /* ## ## S002A4S04 сделать выборку экземпляров по заданным
        условиям поиска (поиск в представлении) */
        $continuous = new ContinuousFilter(
            $context['price'], 'number', '4.50', '9.50'
        );
        $discrete = new DiscreteFilter(
            $context['place-of-production'], 'word', ['Екатеринбург']
        );
        $browser = new Browser($context['PDO']);

        $data = $browser->find($essence, [$continuous]);
        $this->assertNotEmpty($data);

        $data = $browser->find($essence, [$discrete]);
        $this->assertNotEmpty($data);

        $data = $browser->find(
            $essence,
            [$discrete, $continuous]
        );
        $this->assertNotEmpty($data);
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
        $essence = $context['essence'];
        $code = 'new-thing';
        $context['new-thing'] = $code;

        /* добавляем модель, задаём для неё значения атрибутов */
        /* даём модели название */
        $designer = new Designer($linkToData);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $product = $designer->product(
            $code,
            'новая модель',
            'Описание позиции каталога',
        );

        $definition = [
            $context['price'] => '11.11',
            $context['production-date'] => '20210531T0306',
            $context['place-of-production'] => 'Екатеринбург',
        ];
        /* устанавливаем значения для характеристик модели */
        $line = new ProductionLine($linkToData, $code);
        $line->setup($essence, $definition);

        $this->assertTrue(
            true,
            'Item must be created with success'
        );

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
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->change(Storable::DIRECT_READING);
        $schema->refresh();

        $browser = new Browser($context['PDO']);
        $data = $browser->find($context['essence'], []);
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
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->change(Storable::RAPID_OBTAINMENT);
        $schema->refresh();

        $browser = new Browser($context['PDO']);
        $data = $browser->find($context['essence'], []);
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
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->change(Storable::RAPID_RECORDING);
        $schema->refresh();

        $browser = new Browser($context['PDO']);
        $data = $browser->find($context['essence'], []);
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
                'DataType' => 'word',
                'RangeType' => 'discrete',
            ],
        ];

        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $designer = new Designer($linkToData);
        foreach ($codes as $code => $settings) {
            $attribute = $designer->attribute(
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

            /* Добавим сущности cake новую характеристику package */
            (new Redactor($linkToData, $essence))
                ->expand(['package']);
            /* Добавим у всех моделей каталога cake значения для
            характеристики package */
            $manager = new Operator($linkToData, $essence);
            $manager->expand(
                'package',
                'без упаковки'
            );
        }

        /* Добавим существующим моделям новую характеристику. */
        /* Зададим значения новой характеристики для всех моделей. */
        $productList = [
            'bun-with-jam' => 'без упаковки',
            'bun-with-raisins' => 'без упаковки',
            'cinnamon-bun' => 'пакет',
            'new-thing' => 'пакет',
        ];
        foreach ($productList as $product => $value) {
            $context[$product] = $product;

            (new ProductionLine($linkToData, $product))
                ->update(['package' => $value]);
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
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->change(Storable::DIRECT_READING);
        $schema->setup();

        $browser = new Browser($context['PDO']);
        $data = $browser->find($context['essence'], []);
        $this->checkShowAll($context, $data, true, true);
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
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->change(Storable::RAPID_OBTAINMENT);
        $schema->setup();

        $browser = new Browser($context['PDO']);
        $data = $browser->find($context['essence'], []);
        $this->checkShowAll($context, $data, true, true);
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
        $essence = $context['essence'];
        $linkToData = $context['PDO'];
        $schema = new StorageManager($linkToData, $essence,);

        $schema->change(Storable::RAPID_RECORDING);
        //$schema->setup();

        /* $schema->setup('package', 'word'); */
        /*        $schema->setup('package');

                $productList = [
                    'bun-with-jam' => 'без упаковки',
                    'bun-with-raisins' => 'без упаковки',
                    'cinnamon-bun' => 'пакет',
                    'new-thing' => 'пакет',
                ];
                foreach ($productList as $product => $content) {
                    $value = (new Crossover())->setContent($content);
                    $value->setLeftValue($product)->setRightValue('package');
                    $schema->refresh([$value]);
                }*/

        $browser = new Browser($linkToData);
        $data = $browser->find($essence, []);
        $this->checkShowAll($context, $data, true, true);
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
        $linkToData = $context['PDO'];
        $product = $context['new-thing'];
        $attribute = $context['package'];

        $line = new ProductionLine($linkToData, $product);
        $line->update([$attribute => 'коробка']);

        $this->assertTrue(
            true,
            'Content must be changed with success'
        );

        return $context;
    }

    /**
     * Проверим, что значение изменилось в представление
     * @depends testAddNewKind
     *
     * @param array $context
     * @throws Exception
     */
    public function testChangeContentWithinView(array $context)
    {
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->change(Storable::DIRECT_READING);
        $schema->refresh();

        $browser = new Browser($context['PDO']);
        $data = $browser->find($context['essence'], []);
        $this->checkShowAll(
            $context,
            $data,
            true,
            true,
            true
        );
    }

    /**
     * Проверим, что значение изменилось в
     * материализованном представлении
     *
     * @depends testAddNewKind
     *
     * @param array $context
     *
     * @throws Exception
     */
    public function testChangeContentWithinMathView(array $context)
    {
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->change(Storable::RAPID_OBTAINMENT);
        $schema->refresh();

        $browser = new Browser($context['PDO']);
        $data = $browser->find($context['essence'], []);
        $this->checkShowAll(
            $context,
            $data,
            true,
            true,
            true
        );
    }

    /**
     * Проверим, что значение изменилось в таблице
     * @depends testAddNewKind
     *
     * @param array $context
     * @throws Exception
     */
    public function testChangeContentWithinTable(array $context)
    {
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->change(Storable::RAPID_RECORDING);

        /*        $content = (new Crossover())->setContent('коробка');
                $content->setLeftValue($context['new-thing'])
                    ->setRightValue($context['package']);
                $schema->refresh([$content]);*/

        $browser = new Browser($context['PDO']);
        $data = $browser->find($context['essence'], []);
        $this->checkShowAll(
            $context,
            $data,
            true,
            true,
            true
        );
    }

    /**
     * Удаляем характеристику
     *
     * @depends testAddNewKind
     *
     * @param array $context
     *
     * @return array
     * @throws Exception
     */
    public function testUnlinkKind(array $context): array
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        /* Удалим у сущности cake характеристику package */
        (new Redactor($linkToData, $essence))
            ->reduce(['package']);
        /* Удалим у всех моделей каталога cake значения для
        характеристики package */
        $manager = new Operator($linkToData, $essence);
        $manager->reduce('package');

        $this->assertTrue(true);

        return $context;
    }

    /**
     * Пересоздадим представление и проверим, что удалённая
     * характеристика отсутствует
     *
     * @depends testUnlinkKind
     *
     * @param array $context
     *
     * @throws Exception
     */
    public function testUnlinkKindWithView(array $context)
    {
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->change(Storable::DIRECT_READING);
        $schema->prune('package');

        $browser = new Browser($context['PDO']);
        $data = $browser->find($context['essence'], []);
        $this->checkShowAll($context, $data, true);
    }

    /**
     * Пересоздадим материализованное представление и проверим,
     * что удалённая характеристика отсутствует
     *
     * @depends testUnlinkKind
     *
     * @param array $context
     *
     * @throws Exception
     */
    public function testUnlinkKindWithMathView(array $context)
    {
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->change(Storable::RAPID_OBTAINMENT);
        $schema->prune('package');

        $browser = new Browser($context['PDO']);
        $data = $browser->find($context['essence'], []);
        $this->checkShowAll($context, $data, true);
    }

    /**
     * Удалим характеристику из таблицы и проверим,
     * что удалённая характеристика отсутствует
     *
     * @depends testUnlinkKind
     *
     * @param array $context
     *
     * @throws Exception
     */
    public function testUnlinkKindWithTable(array $context)
    {
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->change(Storable::RAPID_RECORDING);
        /*$schema->prune('package');*/

        $browser = new Browser($context['PDO']);
        $data = $browser->find($context['essence'], []);
        $this->checkShowAll($context, $data, true);
    }

    /**
     * Удаляем модель
     *
     * @depends testAddNewItem
     *
     * @param array $context
     *
     * @return array
     * @throws Exception
     */
    public function testRemoveItem(array $context): array
    {
        $linkToData = $context['PDO'];
        $item = $context['new-thing'];

        /* Удаляем модель */
        $productionLine = new ProductionLine($linkToData, $item);
        $productionLine->delete();

        $this->assertTrue(
            true,
            'Item must be created with success',
        );

        return $context;
    }

    /**
     * Проверяем, что модель отсутствует в представлении
     *
     * @depends testAddNewItem
     *
     * @param array $context
     *
     * @throws Exception
     */
    public function testRemoveItemWithView(array $context)
    {
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->change(Storable::DIRECT_READING);
        $schema->refresh();

        $browser = new Browser($context['PDO']);
        $data = $browser->find($context['essence'], []);
        $this->checkShowAll($context, $data);
    }

    /**
     * Проверяем, что модель
     * отсутствует в материализованном представлении
     *
     * @depends testAddNewItem
     *
     * @param array $context
     *
     * @throws Exception
     */
    public function testRemoveItemWithMathView(array $context)
    {
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->change(Storable::RAPID_OBTAINMENT);
        $schema->refresh();

        $browser = new Browser($context['PDO']);
        $data = $browser->find($context['essence'], []);
        $this->checkShowAll($context, $data);
    }

    /**
     * Проверяем, что модель отсутствует в таблице
     *
     * @depends testAddNewItem
     *
     * @param array $context
     *
     * @throws Exception
     */
    public function testRemoveItemWithTable(array $context)
    {
        $schema = new StorageManager(
            $context['PDO'],
            $context['essence']
        );
        $schema->change(Storable::RAPID_RECORDING);
        $schema->refresh();

        $browser = new Browser($context['PDO']);
        $data = $browser->find($context['essence'], []);
        $this->checkShowAll($context, $data);
    }

    /**
     * Удаляем модель
     *
     * @depends testAddNewItem
     *
     * @param array $context
     *
     * @return array
     * @throws Exception
     */
    public function testRemoveCategory(array $context): array
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        /* Удаляем все модели */
        (new Operator($linkToData, $essence))->delete();
        /* удаляем сущность */
        (new Redactor($linkToData, $essence))->delete();

        $this->assertTrue(
            true,
            'Category must be removed with success',
        );

        return $context;
    }

    /**
     * Заключительные действия, откатываем транзакцию
     *
     * @depends testInit
     *
     * @param array $context
     */
    public function testFinally(array $context)
    {
        $isSuccess = static::USE_TRANSACTION;
        if (static::USE_TRANSACTION) {
            $linkToData = $context['PDO'];
            $isSuccess = $linkToData->rollBack();
        }
        $this->assertTrue(
            $isSuccess,
            'Transaction must be rolled back'
        );
    }
}
