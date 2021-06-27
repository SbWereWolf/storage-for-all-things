<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 29.05.2021, 4:53
 */

use AllThings\Content\ContentManager;
use AllThings\DataAccess\Manager\NamedEntityManager;
use AllThings\DataObject\ContinuousFilter;
use AllThings\DataObject\Crossover;
use AllThings\DataObject\DiscreteFilter;
use AllThings\DataObject\ICrossover;
use AllThings\DataObject\NamedEntity;
use AllThings\DirectReading;
use AllThings\Essence\Attribute;
use AllThings\Essence\Essence;
use AllThings\Essence\EssenceAttributeManager;
use AllThings\Essence\EssenceThingManager;
use AllThings\RapidObtainment;
use AllThings\RapidRecording;
use AllThings\SearchEngine\Seeker;
use AllThings\StorageEngine\Installation;
use Environment\DbConnection;
use PHPUnit\Framework\TestCase;

class BusinessProcessTest extends TestCase
{
    public const SKIP = false;

    /**
     * @return array
     */
    public function testInit()
    {
        define(
            'APPLICATION_ROOT',
            realpath(__DIR__)
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '..'
        );
        define(
            'CONFIGURATION_ROOT',
            APPLICATION_ROOT
            . DIRECTORY_SEPARATOR . 'configuration'
        );
        define(
            'DB_READ_CONFIGURATION',
            CONFIGURATION_ROOT
            . DIRECTORY_SEPARATOR . 'db_test.php'
        );

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
     * @depends testInit
     *
     * @param array $context
     *
     * @return array
     */
    public function testEssenceCreate(array $context)
    {
        /* ## S001A1S01 создать сущность для предметов типа "пирожок" */
        $essence = Essence::GetDefaultEssence();
        $essence->setCode('cake');

        $linkToData = $context['PDO'];
        $handler = new AllThings\Essence\EssenceManager(
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
     * @depends testEssenceCreate
     *
     * @param array $context
     *
     * @return array
     */
    public function testSetupEssence(array $context)
    {
        /* ## S001A1S02 задать свойства сущности */
        $code = $context['essence'];
        $value = Essence::GetDefaultEssence();
        $value->setCode($code);
        $value->setTitle('The Cakes');
        $value->setRemark('Cakes  of all kinds');
        $value->setStoreAt('view');

        $linkToData = $context['PDO'];
        $handler = new AllThings\Essence\EssenceManager(
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
     * @depends testEssenceCreate
     *
     * @param array $context
     *
     * @return array
     */
    public function testAttributesCreate(array $context)
    {
        /* ## S001A1S03 создать характеристику */
        $code = 'price';
        $context[$code] = $code;
        $this->createAttribute($context, $code);

        $code = 'production-date';
        $context[$code] = $code;
        $this->createAttribute($context, $code);

        $code = 'place-of-production';
        $context[$code] = $code;
        $this->createAttribute($context, $code);

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

        $linkToData = $context['PDO'];
        $handler = new AllThings\Essence\AttributeManager(
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
     * @depends testAttributesCreate
     *
     * @param array $context
     */
    public function testSetupAttributes(array $context)
    {
        /* ## S001A1S04 задать свойства характеристики */
        $linkToData = $context['PDO'];

        $code = $context['price'];
        $value = Attribute::GetDefaultAttribute();
        $value->setCode($code);
        $value->setTitle('цена, руб.');
        $value->setDataType('decimal');
        $value->setRangeType('continuous');
        $handler = new AllThings\Essence\AttributeManager(
            $value,
            $linkToData
        );

        $isSuccess = $handler->correct($code);
        $this->assertTrue(
            $isSuccess,
            "Attribute `$code` must be updated with success"
        );

        $code = $context['production-date'];
        $value = Attribute::GetDefaultAttribute();
        $value->setCode($code);
        $value->setTitle('дата выработки');
        $value->setDataType('timestamp');
        $value->setRangeType('continuous');
        $handler = new AllThings\Essence\AttributeManager(
            $value,
            $linkToData
        );

        $isSuccess = $handler->correct($code);
        $this->assertTrue(
            $isSuccess,
            "Attribute `$code` must be updated with success"
        );

        $code = $context['place-of-production'];
        $value = Attribute::GetDefaultAttribute();
        $value->setCode($code);
        $value->setTitle('Место производства');
        $value->setDataType('symbol');
        $value->setRangeType('discrete');
        $handler = new AllThings\Essence\AttributeManager(
            $value,
            $linkToData
        );

        $isSuccess = $handler->correct($code);
        $this->assertTrue(
            $isSuccess,
            "Attribute `$code` must be updated with success"
        );
    }

    /**
     * @depends testAttributesCreate
     *
     * @param array $context
     *
     * @return array
     */
    public function testDefineEssence(array $context)
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
        $manager = new EssenceAttributeManager(
            $essence, $attribute,
            $linkToData
        );

        $isSuccess = $manager->setUp();
        $this->assertTrue(
            $isSuccess,
            "Attribute `$attribute` must be linked to"
            . " essence `$essence` with success"
        );
    }

    /**
     * @depends testDefineEssence
     *
     * @param array $context
     *
     * @return array
     */
    public function testThingsCreate(array $context)
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
     * @param $essence
     * @param $linkToData
     *
     * @return array
     */
    private function getEssenceAttributes(
        string $essence,
        PDO $linkToData
    ): array {
        $manager = new EssenceAttributeManager($essence, '', $linkToData);
        $isSuccess = $manager->getAssociated();
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
        $handler = new ContentManager($content, $linkToData);

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
     * @return bool
     */
    private function linkThingToEssence(
        $essence,
        string $code,
        $linkToData
    ): bool {
        $manager = new EssenceThingManager(
            $essence, $code,
            $linkToData
        );
        $isSuccess = $manager->setUp();
        $this->assertTrue(
            $isSuccess,
            "Thing `$code` must be linked"
            . " to essence `$essence` with success"
        );
        return $isSuccess;
    }

    /**
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
     * @depends testThingsCreate
     *
     * @param array $context
     *
     * @return array
     */
    public function testDefineThings(array $context)
    {
        /* ## S001A2S03 задать значения для характеристики предмета */
        $linkToData = $context['PDO'];

        $thing = $context['bun-with-jam'];

        $this->defineThingAttributeValue(
            $thing,
            $context['price'],
            '15.50',
            $linkToData
        );
        $this->defineThingAttributeValue(
            $thing,
            $context['production-date'],
            '20180429T1356',
            $linkToData
        );
        $this->defineThingAttributeValue(
            $thing,
            $context['place-of-production'],
            'Екатеринбург',
            $linkToData
        );

        $thing = $context['bun-with-raisins'];

        $this->defineThingAttributeValue(
            $thing,
            $context['price'],
            '9.50',
            $linkToData
        );
        $this->defineThingAttributeValue(
            $thing,
            $context['production-date'],
            '20180427',
            $linkToData
        );
        $this->defineThingAttributeValue(
            $thing,
            $context['place-of-production'],
            'Екатеринбург',
            $linkToData
        );

        $thing = $context['cinnamon-bun'];

        $this->defineThingAttributeValue(
            $thing,
            'price',
            '4.50',
            $linkToData
        );
        $this->defineThingAttributeValue(
            $thing,
            $context['production-date'],
            '20180429',
            $linkToData
        );
        $this->defineThingAttributeValue(
            $thing,
            $context['place-of-production'],
            'Челябинск',
            $linkToData
        );

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
    ): void {
        $handler = new ContentManager($value, $linkToData);
        $isSuccess = $handler->store($value);
        $this->assertTrue(
            $isSuccess,
            "Attribute `$attribute` of thing `$thing`"
            . ' must be defined with success'
        );
    }

    /**
     * @depends testDefineEssence
     *
     * @param array $context
     */
    public function testCreateView(array $context)
    {
        /* S001A4S02 создать представление */
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $handler = new DirectReading\Source(
            $essence, $linkToData
        );
        $this->checkSourceSetup($handler, $essence);
    }

    /**
     * @param $handler
     * @param $essence
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

        $source = new DirectReading\Source(
            $essence, $linkToData
        );
        $seeker = new Seeker($source);
        $this->checkShowAll($context, $seeker, $essence);
    }

    /**
     * @param array $context
     * @param $seeker
     * @param $essence
     * @param bool $extended
     */
    private function checkShowAll(
        array $context,
        Seeker $seeker,
        string $essence,
        bool $extended = false
    ): void {
        $data = $seeker->data();

        $properNumbers = 3;
        if ($extended) {
            $properNumbers = 4;
        }
        $isEnough = (count($data) === $properNumbers && !$extended)
            || (count($data) === $properNumbers && $extended);
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
                    $this->assertTrue(
                        $isProper,
                        "Thing `$code`"
                        . ' must have same content as defined'
                    );
                    $thingTested++;
                    break;
            }
        }

        $isEnough = ($thingTested === $properNumbers && !$extended)
            || ($thingTested === $properNumbers && $extended);
        $this->assertTrue(
            $isEnough,
            "Each thing of essence `$essence`"
            . ' must be tested for matching with defined'
        );
    }

    /**
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

        $source = new DirectReading\Source(
            $essence, $linkToData
        );
        $seeker = new Seeker($source);
        $this->checkFilters($seeker, $essence);
    }

    /**
     * @param $seeker
     * @param $essence
     */
    private function checkFilters(
        Seeker $seeker,
        string $essence
    ): void {
        $data = $seeker->filters();

        $this->assertTrue(
            count($data) === 2,
            "Filters of essence `$essence`"
            . ' must have two types'
        );
        $this->assertTrue(
            array_key_exists('continuous', $data),
            "Filters of essence `$essence`"
            . ' must have type continuous'
        );
        $this->assertTrue(
            array_key_exists('discrete', $data),
            "Filters of essence `$essence`"
            . ' must have type discrete'
        );

        $filtersValue = 'a:2:{s:10:"continuous";a:4:{s:9:"max@price";s:4:"9.50";s:9:"min@price";s:5:"15.50";s:19:"max@production-date";s:13:"20180429T1356";s:19:"min@production-date";s:8:"20180427";}s:8:"discrete";a:1:{s:19:"place-of-production";a:2:{i:0;s:24:"Екатеринбург";i:1;s:18:"Челябинск";}}}';
        $this->assertTrue(
            serialize($data) === $filtersValue,
            "Filters of essence `$essence` must have proper value"
        );
    }

    /**
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
        $source = new DirectReading\Source(
            $essence, $linkToData
        );
        $seeker = new Seeker($source);

        $this->checkSearch($context, $seeker);
    }

    /**
     * @param array $context
     * @param $seeker
     */
    private function checkSearch(array $context, Seeker $seeker): void
    {
        $continuous = new ContinuousFilter(
            $context['price'], '15.50', '4.50'
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
     * @depends testDefineEssence
     *
     * @param array $context
     */
    public function testCreateMathView(array $context)
    {
        /* S001A4S02 создать материализованное представление */
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $handler = new RapidObtainment\Source($essence, $linkToData);
        $this->checkSourceSetup($handler, $essence);
    }

    /**
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

        $source = new RapidObtainment\Source($essence, $linkToData);
        $seeker = new Seeker($source);
        $this->checkShowAll($context, $seeker, $essence);
    }

    /**
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

        $source = new RapidObtainment\Source($essence, $linkToData);
        $seeker = new Seeker($source);
        $this->checkFilters($seeker, $essence);
    }

    /**
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
        $source = new RapidObtainment\Source($essence, $linkToData);
        $seeker = new Seeker($source);

        $this->checkSearch($context, $seeker);
    }

    /**
     * @depends testDefineEssence
     *
     * @param array $context
     */
    public function testCreateTable(array $context)
    {
        /* S001A4S02 создать представление */
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $handler = new RapidRecording\Source(
            $essence, $linkToData
        );
        $this->checkSourceSetup($handler, $essence);
    }

    /**
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

        $source = new RapidRecording\Source($essence, $linkToData);
        $seeker = new Seeker($source);
        $this->checkShowAll($context, $seeker, $essence);
    }

    /**
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

        $source = new RapidRecording\Source($essence, $linkToData);
        $seeker = new Seeker($source);
        $this->checkFilters($seeker, $essence);
    }

    /**
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
        $source = new RapidRecording\Source($essence, $linkToData);
        $seeker = new Seeker($source);

        $this->checkSearch($context, $seeker);
    }

    /**
     * @depends testThingsCreate
     *
     * @param array $context
     */
    public function testAddNewThing(array $context)
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
     * @depends testAddNewThing
     *
     * @param array $context
     */
    public function testAddNewThingToView(array $context)
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new DirectReading\Source(
            $essence, $linkToData
        );
        $isSuccess = $source->refresh();
        $this->assertTrue(
            $isSuccess,
            'View MUST BE refreshed with success'
        );

        $seeker = new Seeker($source);
        $this->checkShowAll($context, $seeker, $essence, true);
    }

    /**
     * @depends testAddNewThing
     *
     * @param array $context
     */
    public function testAddNewThingToMathView(array $context)
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new RapidObtainment\Source($essence, $linkToData);
        $isSuccess = $source->refresh();
        $this->assertTrue(
            $isSuccess,
            'MathView MUST BE refreshed with success'
        );

        $seeker = new Seeker($source);
        $this->checkShowAll($context, $seeker, $essence, true);
    }

    /**
     * @depends testAddNewThing
     *
     * @param array $context
     */
    public function testAddNewThingToTable(array $context)
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new RapidRecording\Source($essence, $linkToData);
        $isSuccess = $source->refresh();
        $this->assertTrue(
            $isSuccess,
            'Table MUST BE refreshed with success'
        );

        $seeker = new Seeker($source);
        $this->checkShowAll($context, $seeker, $essence, true);
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
     * @depends testAddNewThing
     *
     * @param array $context
     *
     * @return array
     */
    public function testAddNewAttribute(array $context)
    {
        $linkToData = $context['PDO'];

        /* Добавляем новую характеристику package и задаём параметры
        этой характеристики */
        $code = 'package';
        $context[$code] = $code;
        $this->createAttribute($context, $code);
        $value = Attribute::GetDefaultAttribute();
        $value->setCode($code);
        $value->setTitle('Упаковка');
        $value->setDataType('symbol');
        $value->setRangeType('discrete');
        $handler = new AllThings\Essence\AttributeManager(
            $value,
            $linkToData
        );
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

        return $context;
    }

    /**
     * @depends testAddNewAttribute
     *
     * @param array $context
     */
    public function testAddNewAttributeToView(array $context)
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new DirectReading\Source(
            $essence, $linkToData
        );
        $isSuccess = $source->setup();
        $this->assertTrue(
            $isSuccess,
            'View MUST BE recreated with success'
        );

        $seeker = new Seeker($source);
        $this->checkShowAll($context, $seeker, $essence, true);
    }

    /**
     * @depends testAddNewAttribute
     *
     * @param array $context
     */
    public function testAddNewAttributeToMathView(array $context)
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new RapidObtainment\Source($essence, $linkToData);
        $isSuccess = $source->setup();
        $this->assertTrue(
            $isSuccess,
            'MathView MUST BE recreated with success'
        );

        $seeker = new Seeker($source);
        $this->checkShowAll($context, $seeker, $essence, true);
    }

    /**
     * @depends testAddNewAttribute
     *
     * @param array $context
     */
    public function testAddNewAttributeToTable(array $context)
    {
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $source = new RapidRecording\Source($essence, $linkToData);
        $isSuccess = $source->setup();
        $this->assertTrue(
            $isSuccess,
            'Table MUST BE recreated with success'
        );

        $seeker = new Seeker($source);
        $this->checkShowAll($context, $seeker, $essence, true);
    }

    /**
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
