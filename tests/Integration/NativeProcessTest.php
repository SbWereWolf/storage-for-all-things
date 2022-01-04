<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 04.01.2022, 10:06
 */

namespace Integration;

use AllThings\Blueprint\Attribute\Attribute;
use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\Blueprint\Essence\Essence;
use AllThings\Blueprint\Essence\EssenceManager;
use AllThings\Blueprint\Specification\SpecificationManager;
use AllThings\Catalog\CatalogManager;
use AllThings\Content\ContentManager;
use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Crossover\CrossoverTable;
use AllThings\DataAccess\Crossover\ICrossover;
use AllThings\DataAccess\Nameable\NamedEntity;
use AllThings\DataAccess\Nameable\NamedEntityManager;
use AllThings\SearchEngine\ContinuousFilter;
use AllThings\SearchEngine\DiscreteFilter;
use AllThings\SearchEngine\Seeker;
use AllThings\StorageEngine\DirectReading;
use AllThings\StorageEngine\Installation;
use AllThings\StorageEngine\RapidObtainment;
use AllThings\StorageEngine\RapidRecording;
use Environment\Database\PdoConnection;
use Exception;
use PDO;
use PHPUnit\Framework\TestCase;

class NativeProcessTest extends TestCase
{
    public const SKIP = false;

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
    public function testEssenceCreate(array $context): array
    {
        /* ## S001A1S01 создать сущность для предметов типа "пирожок" */
        $essence = Essence::GetDefaultEssence();
        $essence->setCode('cake');

        $linkToData = $context['PDO'];
        $handler = new EssenceManager(
            $essence,
            $linkToData
        );

        $isSuccess = $handler->create();
        $this->assertTrue(
            $isSuccess,
            'Essence must be created with success'
        );

        $context['essence'] = 'cake';

        return $context;
    }

    /**
     * Задаём название и описание сущности
     * @depends testEssenceCreate
     *
     * @param array $context
     *
     * @return array
     * @throws Exception
     */
    public function testSetupEssence(array $context): array
    {
        /* ## S001A1S02 задать свойства сущности */
        $code = $context['essence'];
        $value = Essence::GetDefaultEssence();
        $value->setCode($code);
        $value->setTitle('The Cakes');
        $value->setRemark('Cakes  of all kinds');
        $value->setStorageKind('view');

        $linkToData = $context['PDO'];
        $handler = new EssenceManager(
            $value,
            $linkToData
        );


        $isSuccess = $handler->correct($code);
        $this->assertTrue(
            $isSuccess,
            'Essence must be updated with success'
        );

        return $context;
    }

    /**
     * Создаём характеристики
     * @depends testEssenceCreate
     *
     * @param array $context
     *
     * @return array
     */
    public function testAttributesCreate(array $context): array
    {
        /* ## S001A1S03 создать характеристику */
        $codes = ['price', 'production-date', 'place-of-production',];
        foreach ($codes as $code) {
            $context = $this->addAttributeToContext($code, $context);
        }

        return $context;
    }

    /**
     * @param array $context
     * @param string $code
     */
    private function createAttribute(array $context, string $code): void
    {
        $attribute = (Attribute::GetDefaultAttribute());
        $attribute->setCode($code);
        switch ($code) {
            case 'price':
                $attribute->setDataType('number');
                $attribute->setRangeType('continuous');
                break;
            case 'production-date':
                $attribute->setDataType('time');
                $attribute->setRangeType('continuous');
                break;
            case 'place-of-production':
                $attribute->setDataType('word');
                $attribute->setRangeType('discrete');
                break;
            case 'package':
                $attribute->setDataType('word');
                $attribute->setRangeType('discrete');
                break;
        }

        $linkToData = $context['PDO'];
        $handler = new AttributeManager(
            $attribute,
            $linkToData
        );

        $isSuccess = $handler->create();
        $this->assertTrue(
            $isSuccess,
            "Attribute $code must be created with success"
        );
    }

    /**
     * Задаём свойства атрибутов
     * @depends testAttributesCreate
     *
     * @param array $context
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function testSetupAttributes(array $context)
    {
        /* ## S001A1S04 задать свойства характеристики */
        $linkToData = $context['PDO'];

        $codes = [
            $context['price'] => [
                'Title' => 'цена, руб.',
                'DataType' => 'number',
                'RangeType' => 'continuous',
            ],
            $context['production-date'] => [
                'Title' => 'дата выработки',
                'DataType' => 'time',
                'RangeType' => 'continuous',
            ],
            $context['place-of-production'] => [
                'Title' => 'Место производства',
                'DataType' => 'word',
                'RangeType' => 'discrete',
            ],
        ];

        foreach ($codes as $code => $settings) {
            $value = Attribute::GetDefaultAttribute();
            $value->setCode($code)
                ->setTitle($settings['Title'])
                ->setDataType($settings['DataType'])
                ->setRangeType($settings['RangeType']);
            $handler = new AttributeManager(
                $value,
                $linkToData
            );

            $isSuccess = $handler->correct($code);
            $this->assertTrue(
                $isSuccess,
                "Attribute `$code` must be updated with success"
            );
        }
    }

    /**
     * Задаём характеристики для сущности
     * @depends testAttributesCreate
     *
     * @param array $context
     *
     * @return array
     */
    public function testDefineEssence(array $context): array
    {
        /* ## S001A1S05 охарактеризовать сущность (назначить
         характеристики для предметов этого типа) */
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $attributes = ['price', 'production-date', 'place-of-production'];
        foreach ($attributes as $attribute) {
            $this->LinkEssenceToAttribute(
                $essence,
                $attribute,
                $linkToData
            );
        }

        return $context;
    }

    /**
     * @param $essence
     * @param $attribute
     * @param $linkToData
     */
    private function LinkEssenceToAttribute(
        $essence,
        $attribute,
        $linkToData
    ): void {
        $manager = new SpecificationManager(
            $linkToData
        );

        $linkage = (new Crossover())
            ->setLeftValue($essence)
            ->setRightValue($attribute);
        $isSuccess = $manager->linkUp($linkage);
        $this->assertTrue(
            $isSuccess,
            "Attribute `$attribute` must be linked to"
            . " essence `$essence` with success"
        );
    }

    /** Создаём модели на основе сущности
     * @depends testDefineEssence
     *
     * @param array $context
     *
     * @return array
     */
    public function testThingsCreate(array $context): array
    {
        /* ## S001A2S01 создать предметы типа "пирожок"
        (создать пирожки) */
        $linkToData = $context['PDO'];
        $essence = $context['essence'];
        $attributes = $this->getEssenceAttributes(
            $essence,
            $linkToData
        );

        $codes = ['bun-with-jam', 'bun-with-raisins', 'cinnamon-bun'];
        foreach ($codes as $thing) {
            $context[$thing] = $thing;
            $this->createThing($thing, $linkToData);

            foreach ($attributes as $attribute) {
                $this->defineThing($thing, $attribute, $linkToData);
            }

            $this->linkThingToEssence($essence, $thing, $linkToData);
        }

        return $context;
    }

    /**
     * @param string $essence
     * @param PDO $linkToData
     *
     * @return array
     */
    private function getEssenceAttributes(
        string $essence,
        PDO $linkToData
    ): array {
        $manager = new SpecificationManager($linkToData);
        $linkage = (new Crossover())->setLeftValue($essence);
        $isSuccess = $manager->getAssociated($linkage);
        $this->assertTrue(
            $isSuccess,
            "Attributes of essence `$essence`"
            . " must be fetched with success"
        );
        if ($isSuccess) {
            $isSuccess = $manager->has();
            $this->assertTrue(
                $isSuccess,
                "Essence `$essence` must be linked to some attributes"
            );
        }
        $attributes = [];
        if ($isSuccess) {
            $attributes = $manager->retrieveData();
        }

        return $attributes;
    }

    /**
     * @param string $code
     * @param        $linkToData
     */
    private function createThing(string $code, $linkToData): void
    {
        $nameable = (new NamedEntity())->setCode($code);
        $handler = new NamedEntityManager($nameable, 'thing', $linkToData);

        $isSuccess = $handler->create();
        $this->assertTrue(
            $isSuccess,
            "Thing ` $code ` must be created with success"
        );
    }

    /**
     * @param $thing
     * @param $attribute
     * @param $linkToData
     */
    private function defineThing($thing, $attribute, $linkToData)
    {
        $content = (new Crossover())->setLeftValue($thing)
            ->setRightValue($attribute);

        $table = SpecificationManager::getLocation(
            $attribute,
            $linkToData
        );
        $contentTable = new CrossoverTable(
            $table,
            'thing_id',
            'attribute_id'
        );
        $handler = new ContentManager($content, $linkToData, $contentTable);

        $isSuccess = $handler->attach();
        $this->assertTrue(
            $isSuccess,
            "Attribute `$attribute` must be defined"
            . " for thing `$thing` with success"
        );
    }

    /**
     * @param        $essence
     * @param string $code
     * @param        $linkToData
     *
     * @return void
     */
    private function linkThingToEssence(
        $essence,
        string $code,
        $linkToData
    ): void
    {
        $manager = new CatalogManager(
            $essence,
            $code,
            $linkToData
        );
        $linkage = (new Crossover())
            ->setLeftValue($essence)
            ->setRightValue($code);
        $isSuccess = $manager->linkUp($linkage);
        $this->assertTrue(
            $isSuccess,
            "Thing `$code` must be linked"
            . " to essence `$essence` with success"
        );
    }

    /**
     * Задаём свойства моделям
     * @depends testThingsCreate
     *
     * @param array $context
     */
    public function testSetupThing(array $context)
    {
        /* ## S001A2S02 задать значения свойствам предметов
        (дать имена пирожкам) */
        $linkToData = $context['PDO'];

        $titles = [];
        $titles['bun-with-jam'] = 'Булочка с повидлом';
        $titles['bun-with-raisins'] = 'Булочка с изюмом';
        $titles['cinnamon-bun'] = 'Булочка с корицей';

        foreach ($titles as $code => $title) {
            $this->updateTitle($code, $title, $linkToData);
        }
    }

    /**
     * @param $code
     * @param $title
     * @param $linkToData
     */
    private function updateTitle($code, $title, $linkToData): void
    {
        $subject = (new NamedEntity())
            ->setCode($code)
            ->setTitle($title);
        $handler = new NamedEntityManager($subject, 'thing', $linkToData);

        $isSuccess = $handler->correct($code);
        $this->assertTrue(
            $isSuccess,
            "Thing `$code` title must be updated with success"
        );
    }

    /**
     * Задаём значения характеристикам моделей
     * @depends testThingsCreate
     *
     * @param array $context
     *
     * @return array
     */
    public function testDefineThings(array $context): array
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

        foreach ($codes as $code => $settings) {
            foreach ($settings as $attribute => $value) {
                $this->defineThingAttributeValue(
                    $code,
                    $attribute,
                    $value,
                    $linkToData
                );
            }
        }

        return $context;
    }

    /**
     * @param string $thing
     * @param string $attribute
     * @param ICrossover $value
     * @param PDO $linkToData
     */
    private function defineContent(
        string $thing,
        string $attribute,
        ICrossover $value,
        PDO $linkToData
    ): void
    {
        $table = SpecificationManager::getLocation(
            $attribute,
            $linkToData
        );

        $contentTable = new CrossoverTable(
            $table,
            'thing_id',
            'attribute_id'
        );
        $handler = new ContentManager($value, $linkToData, $contentTable);
        $isSuccess = $handler->store($value);
        $this->assertTrue(
            $isSuccess,
            "Attribute `$attribute` of thing `$thing`"
            . ' must be defined with success'
        );
    }

    /**
     * Создаём представление для характеристик моделей
     * @depends testDefineEssence
     *
     * @param array $context
     */
    public function testCreateView(array $context)
    {
        /* S001A4S02 создать представление */
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $handler = new DirectReading(
            $essence, $linkToData
        );
        $this->checkSourceSetup($handler, $essence);
    }

    /**
     * @param Installation $handler
     * @param string $essence
     */
    private function checkSourceSetup(
        Installation $handler,
        string $essence
    ): void {
        $result = $handler->setup();

        $this->assertTrue(
            $result,
            "DB source for"
            . " `$essence` must be created with success"
        );
    }

    /**
     * Получаем данные всех моделей из представления
     * @depends testDefineThings
     *
     * @param array $context
     */
    public function testShowAllFromView(array $context)
    {
        /* ## S001A4S04 получить данные из представления
        (без фильтрации) */
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new DirectReading(
            $essence, $linkToData
        );
        $seeker = new Seeker($source);
        $data = $seeker->data();
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
                    /** @noinspection PhpConditionAlreadyCheckedInspection */
                    $isProper = $isProper
                        && $thing[$context['price']] === '15.5000';
                    $isProper = $isProper
                        && $thing[$context['production-date']]
                        === '2018-04-29 13:56:00+00';
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
                    /** @noinspection PhpConditionAlreadyCheckedInspection */
                    $isProper = $isProper
                        && $thing[$context['price']] === '9.5000';
                    $isProper = $isProper
                        && $thing[$context['production-date']]
                        === '2018-04-27 00:00:00+00';
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
                    /** @noinspection PhpConditionAlreadyCheckedInspection */
                    $isProper = $isProper
                        && $thing[$context['price']] === '4.5000';
                    $isProper = $isProper
                        && $thing[$context['production-date']]
                        === '2018-04-29 00:00:00+00';
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
                    /** @noinspection PhpConditionAlreadyCheckedInspection */
                    $isProper = $isProper
                        && $thing[$context['price']] === '11.1100';
                    $isProper = $isProper
                        && $thing[$context['production-date']]
                        === '2021-05-31 03:06:00+00';
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
     * @depends testDefineThings
     *
     * @param array $context
     */
    public function testGetFiltersForView(array $context)
    {
        /* ## S002A4S03 определить возможные условия для поиска
        (параметры фильтрации) */
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new DirectReading(
            $essence, $linkToData
        );
        $seeker = new Seeker($source);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $filters = $seeker->filters();

        $this->checkFilters($filters, $essence);
    }

    /**
     * @param array $data
     * @param string $essence
     */
    private function checkFilters(
        array $data,
        string $essence
    ): void
    {
        $filtersValue = 'a:3:{i:0;O:37:"AllThings\SearchEngine\Disc' .
            'reteFilter":2:{s:45:" AllThings\SearchEngine\Discret' .
            'eFilter values";a:2:{i:0;s:24:"Екатеринбург";i:1;s:1' .
            '8:"Челябинск";}s:40:" AllThings\SearchEngine\Filter' .
            ' attribute";s:19:"place-of-production";}i:1;O:39:"Al' .
            'lThings\SearchEngine\ContinuousFilter":3:{s:44:" All' .
            'Things\SearchEngine\ContinuousFilter min";s:6:"4.500' .
            '0";s:44:" AllThings\SearchEngine\ContinuousFilter ' .
            'max";s:7:"15.5000";s:40:" AllThings\SearchEngine\Fil' .
            'ter attribute";s:5:"price";}i:2;O:39:"AllThings\Sear' .
            'chEngine\ContinuousFilter":3:{s:44:" AllThings\Searc' .
            'hEngine\ContinuousFilter min";s:22:"2018-04-27 00:00' .
            ':00+00";s:44:" AllThings\SearchEngine\ContinuousFilt' .
            'er max";s:22:"2018-04-29 13:56:00+00";s:40:" AllTh' .
            'ings\SearchEngine\Filter attribute";s:15:"production' .
            '-date";}}';
        $this->assertTrue(
            serialize($data) === $filtersValue,
            "Filters of essence `$essence` must have proper value"
        );
    }

    /**
     * Фильтруем модели из представления по заданным значениям
     * характеристик
     * @depends testDefineThings
     *
     * @param array $context
     */
    public function testSearchWithinView(array $context)
    {
        /* ## ## S002A4S04 сделать выборку экземпляров по заданным
        условиям поиска (поиск в представлении) */
        $essence = $context['essence'];
        $linkToData = $context['PDO'];
        $source = new DirectReading(
            $essence, $linkToData
        );
        $seeker = new Seeker($source);

        $this->checkSearch($context, $seeker);
    }

    /**
     * @param array $context
     * @param Seeker $seeker
     */
    private function checkSearch(array $context, Seeker $seeker): void
    {
        $continuous = new ContinuousFilter(
            $context['price'], '4.50', '15.50'
        );
        $data = $seeker->data([$continuous]);
        $this->assertTrue(!empty($data));

        $discrete = new DiscreteFilter(
            $context['place-of-production'], ['Челябинск']
        );
        $data = $seeker->data([$discrete]);
        $this->assertTrue(!empty($data));

        $data = $seeker->data([$discrete, $continuous]);
        $this->assertTrue(!empty($data));
    }

    /**
     * Создаём материализованное представление для характеристик
     * моделей
     * @depends testDefineEssence
     *
     * @param array $context
     */
    public function testCreateMathView(array $context)
    {
        /* S001A4S02 создать материализованное представление */
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $handler = new RapidObtainment($essence, $linkToData);
        $this->checkSourceSetup($handler, $essence);
    }

    /**
     * Читаем характеристики моделей из материализованного
     * представления
     * @depends testDefineThings
     *
     * @param array $context
     */
    public function testShowAllFromMathView(array $context)
    {
        /* ## S001A4S04 получить данные из представления
        (без фильтрации) */
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new RapidObtainment($essence, $linkToData);
        $seeker = new Seeker($source);
        $data = $seeker->data();
        $this->checkShowAll($context, $data);
    }

    /**
     * Получаем значения фильтров для поиска моделей в
     * материализованном представлении по значениям характеристик
     * @depends testDefineThings
     *
     * @param array $context
     */
    public function testGetFiltersForMathView(array $context)
    {
        /* ## S002A4S03 определить возможные условия для поиска
        (параметры фильтрации) */
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new RapidObtainment($essence, $linkToData);
        $seeker = new Seeker($source);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $filters = $seeker->filters();

        $this->checkFilters($filters, $essence);
    }

    /**
     * Фильтруем модели из материализованного представления по
     * заданным значениям характеристик
     * @depends testDefineThings
     *
     * @param array $context
     */
    public function testSearchWithinMathView(array $context)
    {
        /* ## ## S002A4S04 сделать выборку экземпляров по заданным
        условиям поиска (поиск в представлении) */
        $essence = $context['essence'];
        $linkToData = $context['PDO'];
        $source = new RapidObtainment($essence, $linkToData);
        $seeker = new Seeker($source);

        $this->checkSearch($context, $seeker);
    }

    /**
     * Создаём таблицу для значений характеристик моделей
     * @depends testDefineEssence
     *
     * @param array $context
     */
    public function testCreateTable(array $context)
    {
        /* S001A4S02 создать представление */
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $handler = new RapidRecording(
            $essence, $linkToData
        );
        $this->checkSourceSetup($handler, $essence);
    }

    /**
     * Получаем характеристики всех моделей из таблицы
     * @depends testDefineThings
     *
     * @param array $context
     */
    public function testShowAllFromTable(array $context)
    {
        /* ## S001A4S04 получить данные из представления
        (без фильтрации) */
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new RapidRecording($essence, $linkToData);
        $seeker = new Seeker($source);
        $data = $seeker->data();
        $this->checkShowAll($context, $data);
    }

    /**
     * Получаем значения фильтров для поиска моделей в таблице
     * по значениям характеристик
     * @depends testDefineThings
     *
     * @param array $context
     */
    public function testGetFiltersForTable(array $context)
    {
        /* ## S002A4S03 определить возможные условия для поиска
        (параметры фильтрации) */
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new RapidRecording($essence, $linkToData);
        $seeker = new Seeker($source);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $filters = $seeker->filters();

        $this->checkFilters($filters, $essence);
    }

    /**
     * Фильтруем модели из таблицы по заданным значениям
     * характеристик
     * @depends testDefineThings
     *
     * @param array $context
     */
    public function testSearchWithinTable(array $context)
    {
        /* ## ## S002A4S04 сделать выборку экземпляров по заданным
        условиям поиска (поиск в представлении) */
        $essence = $context['essence'];
        $linkToData = $context['PDO'];
        $source = new RapidRecording($essence, $linkToData);
        $seeker = new Seeker($source);

        $this->checkSearch($context, $seeker);
    }

    /**
     * Добавляем новую модель
     * @depends testThingsCreate
     *
     * @param array $context
     * @return array
     */
    public function testAddNewThing(array $context): array
    {
        /* получаем атрибуты сущности */
        $linkToData = $context['PDO'];
        $essence = $context['essence'];
        $attributes = $this->getEssenceAttributes(
            $essence,
            $linkToData
        );
        /* добавляем модель, задаём для неё атрибуты */
        $codes = ['new-thing'];
        foreach ($codes as $thing) {
            $context[$thing] = $thing;
            $this->createThing($thing, $linkToData);

            foreach ($attributes as $attribute) {
                $this->defineThing($thing, $attribute, $linkToData);
            }

            $this->linkThingToEssence($essence, $thing, $linkToData);
        }
        /* даём модели название */
        $titles = [];
        $titles['new-thing'] = 'новая модель';
        foreach ($titles as $code => $title) {
            $this->updateTitle($code, $title, $linkToData);
        }
        /* задаём характеристики модели */
        $this->defineThingAttributeValue(
            $context['new-thing'],
            $context['price'],
            '11.11',
            $linkToData
        );
        $this->defineThingAttributeValue(
            $context['new-thing'],
            $context['production-date'],
            '20210531T0306',
            $linkToData
        );
        $this->defineThingAttributeValue(
            $context['new-thing'],
            $context['place-of-production'],
            'Екатеринбург',
            $linkToData
        );

        return $context;
    }

    /**
     * Добавляем новую модель в представление
     * @depends testAddNewThing
     *
     * @param array $context
     */
    public function testAddNewThingToView(array $context)
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new DirectReading(
            $essence,
            $linkToData
        );
        $isSuccess = $source->refresh();
        $this->assertTrue(
            $isSuccess,
            'View MUST BE refreshed with success'
        );

        $seeker = new Seeker($source);
        $data = $seeker->data();
        $this->checkShowAll($context, $data, true);
    }

    /**
     * Добавляем новую модель в материализованное представление
     * @depends testAddNewThing
     *
     * @param array $context
     */
    public function testAddNewThingToMathView(array $context)
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new RapidObtainment($essence, $linkToData);
        $isSuccess = $source->refresh();
        $this->assertTrue(
            $isSuccess,
            'MathView MUST BE refreshed with success'
        );

        $seeker = new Seeker($source);
        $data = $seeker->data();
        $this->checkShowAll($context, $data, true);
    }

    /**
     * Добавляем новую модель в таблицу
     * @depends testAddNewThing
     *
     * @param array $context
     */
    public function testAddNewThingToTable(array $context)
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new RapidRecording($essence, $linkToData);
        $isSuccess = $source->refresh();
        $this->assertTrue(
            $isSuccess,
            'Table MUST BE refreshed with success'
        );

        $seeker = new Seeker($source);
        $data = $seeker->data();
        $this->checkShowAll($context, $data, true);
    }

    /**
     * @param $thing
     * @param $attribute
     * @param string $content
     * @param $linkToData
     */
    private function defineThingAttributeValue(
        $thing,
        $attribute,
        string $content,
        $linkToData
    ) {
        $value = (new Crossover())->setContent($content)
            ->setLeftValue($thing)->setRightValue($attribute);
        $this->defineContent($thing, $attribute, $value, $linkToData);
    }

    /**
     * Добавляем новую характеристику
     * @depends testAddNewThing
     *
     * @param array $context
     *
     * @return array
     */
    public function testAddNewAttribute(array $context): array
    {
        $linkToData = $context['PDO'];

        /* Добавляем новую характеристику package и задаём параметры
        этой характеристики */
        $code = 'package';
        $context = $this->addAttributeToContext($code, $context);
        $value = Attribute::GetDefaultAttribute();
        $value->setCode($code);
        $value->setTitle('Упаковка');
        $value->setDataType('word');
        $value->setRangeType('discrete');
        $handler = new AttributeManager(
            $value,
            $linkToData
        );
        /** @noinspection PhpUnusedLocalVariableInspection */
        $isSuccess = $handler->correct($code);

        /* Добавим сущности cake новую характеристику package */
        $essence = $context['essence'];
        $this->LinkEssenceToAttribute(
            $essence,
            $code,
            $linkToData
        );

        /* Добавим существующим моделям новую характеристику */
        $thingList = [
            'bun-with-jam',
            'bun-with-raisins',
            'cinnamon-bun',
            'new-thing',
        ];
        foreach ($thingList as $thing) {
            $context[$thing] = $thing;
            $this->defineThing($thing, $code, $linkToData);
        }

        /* Зададим значения новой характеристики для всех моделей */
        $this->defineThingAttributeValue(
            $context['bun-with-jam'],
            $context['package'],
            'без упаковки',
            $linkToData
        );
        $this->defineThingAttributeValue(
            $context['bun-with-raisins'],
            $context['package'],
            'без упаковки',
            $linkToData
        );
        $this->defineThingAttributeValue(
            $context['cinnamon-bun'],
            $context['package'],
            'пакет',
            $linkToData
        );
        $this->defineThingAttributeValue(
            $context['new-thing'],
            $context['package'],
            'пакет',
            $linkToData
        );

        return $context;
    }

    /**
     * Добавляем новую характеристику в представление
     * @depends testAddNewAttribute
     *
     * @param array $context
     */
    public function testAddNewAttributeToView(array $context)
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new DirectReading(
            $essence, $linkToData
        );
        $isSuccess = $source->setup();
        $this->assertTrue(
            $isSuccess,
            'View MUST BE recreated with success'
        );

        $seeker = new Seeker($source);
        $data = $seeker->data();
        $this->checkShowAll($context, $data, true);
    }

    /**
     * Добавляем новую характеристику в материализованное
     * представление
     * @depends testAddNewAttribute
     *
     * @param array $context
     */
    public function testAddNewAttributeToMathView(array $context)
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new RapidObtainment($essence, $linkToData);
        $isSuccess = $source->setup();
        $this->assertTrue(
            $isSuccess,
            'MathView MUST BE recreated with success'
        );

        $seeker = new Seeker($source);
        $data = $seeker->data();
        $this->checkShowAll($context, $data, true);
    }

    /**
     * Добавляем новую характеристику в таблицу
     * @depends testAddNewAttribute
     *
     * @param array $context
     */
    public function testAddNewAttributeToTable(array $context)
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new RapidRecording($essence, $linkToData);
        $isSuccess = $source->setup();
        $this->assertTrue(
            $isSuccess,
            'Table MUST BE recreated with success'
        );

        $seeker = new Seeker($source);
        $data = $seeker->data();
        $this->checkShowAll($context, $data, true);
    }

    /**
     * Изменим значение характеристики модели
     * @depends testAddNewAttribute
     *
     * @param array $context
     *
     * @return array
     */
    public function testChangeThingAttribute(array $context): array
    {
        $linkToData = $context['PDO'];
        $this->defineThingAttributeValue(
            $context['new-thing'],
            $context['package'],
            'коробка',
            $linkToData
        );

        return $context;
    }

    /**
     * Добавляем новую модель в представление
     * @depends testAddNewAttribute
     *
     * @param array $context
     */
    public function testChangeThingWithinView(array $context)
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new DirectReading($essence, $linkToData);
        $isSuccess = $this->changeContent($context, $source);
        $this->assertTrue(
            $isSuccess,
            'View MUST BE refreshed with success'
        );

        $seeker = new Seeker($source);
        $data = $seeker->data();
        $this->checkShowAll($context, $data, true, true, true);
    }

    /**
     * Добавляем новую модель в материализованное представление
     * @depends testAddNewAttribute
     *
     * @param array $context
     */
    public function testChangeThingWithinMathView(array $context)
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new RapidObtainment($essence, $linkToData);
        $isSuccess = $this->changeContent($context, $source);
        $this->assertTrue(
            $isSuccess,
            'MathView MUST BE refreshed with success'
        );

        $seeker = new Seeker($source);
        $data = $seeker->data();
        $this->checkShowAll($context, $data, true, true, true);
    }

    /**
     * Добавляем новую модель в таблицу
     * @depends testAddNewAttribute
     *
     * @param array $context
     */
    public function testChangeThingWithinTable(array $context)
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new RapidRecording($essence, $linkToData);
        $isSuccess = $this->changeContent($context, $source);
        $this->assertTrue(
            $isSuccess,
            'Table MUST BE refreshed with success'
        );

        $seeker = new Seeker($source);
        $data = $seeker->data();
        $this->checkShowAll($context, $data, true, true, true);
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

    /**
     * @param string $code
     * @param array $context
     * @return array
     */
    private function addAttributeToContext(string $code, array $context): array
    {
        $context[$code] = $code;
        $this->createAttribute($context, $code);
        return $context;
    }

    /**
     * @param array $context
     * @param Installation $source
     * @return bool
     */
    private function changeContent(
        array $context,
        Installation $source
    ): bool {
        $content = (new Crossover())->
        setLeftValue($context['new-thing'])
            ->setRightValue($context['package'])
            ->setContent('коробка');
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $isSuccess = $source->refresh([$content]);

        return $isSuccess;
    }
}
