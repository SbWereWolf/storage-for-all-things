<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 26.09.2019, 18:08
 */

use AllThings\Content\ContentManager;
use AllThings\DataAccess\Manager\NamedEntityManager;
use AllThings\DataObject\Crossover;
use AllThings\DataObject\ICrossover;
use AllThings\DataObject\NamedEntity;
use AllThings\Essence\Attribute;
use AllThings\Essence\Essence;
use AllThings\Essence\EssenceAttributeManager;
use AllThings\Essence\EssenceThingManager;
use Environment\DbConnection;
use PHPUnit\Framework\TestCase;

class BusinessProcess extends TestCase
{
    const SKIP = false;

    /**
     * @return array
     */
    public function testInit()
    {
        define('APPLICATION_ROOT', realpath(__DIR__)
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '..');
        define('CONFIGURATION_ROOT', APPLICATION_ROOT
            . DIRECTORY_SEPARATOR . 'configuration');
        define('DB_READ_CONFIGURATION', CONFIGURATION_ROOT
            . DIRECTORY_SEPARATOR . 'db_test.php');

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
        $essence = Essence::GetDefaultEssence();
        $essence->setCode('cake');

        $linkToData = $context['PDO'];
        $handler = new AllThings\Essence\EssenceManager($essence,
            $linkToData);

        $isSuccess = $handler->create();
        $this->assertTrue($isSuccess,
            'Essence must be created with success');

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
        $code = $context['essence'];
        $value = Essence::GetDefaultEssence();
        $value->setCode($code);
        $value->setTitle('The Cakes');
        $value->setRemark('Cakes  of all kinds');
        $value->setStoreAt('view');

        $linkToData = $context['PDO'];
        $handler = new AllThings\Essence\EssenceManager($value,
            $linkToData);


        $isSuccess = $handler->correct($code);
        $this->assertTrue($isSuccess,
            'Essence must be updated with success');

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
     * @param array  $context
     * @param string $code
     */
    private function createAttribute(array $context, string $code): void
    {
        $attribute = (Attribute::GetDefaultAttribute());
        $attribute->setCode($code);

        $linkToData = $context['PDO'];
        $handler = new AllThings\Essence\AttributeManager($attribute,
            $linkToData);

        $isSuccess = $handler->create();
        $this->assertTrue($isSuccess,
            'Attribute' . $code . ' must be created with success');
    }

    /**
     * @depends testAttributesCreate
     *
     * @param array $context
     */
    public function testSetupAttributes(array $context)
    {
        $linkToData = $context['PDO'];

        $code = $context['price'];
        $value = Attribute::GetDefaultAttribute();
        $value->setCode($code);
        $value->setTitle('цена, руб.');
        $value->setDataType('decimal');
        $value->setRangeType('continuous');
        $handler = new AllThings\Essence\AttributeManager($value,
            $linkToData);

        $isSuccess = $handler->correct($code);
        $this->assertTrue($isSuccess,
            'Attribute `' . $code . '`must be updated with success');

        $code = $context['production-date'];
        $value = Attribute::GetDefaultAttribute();
        $value->setCode($code);
        $value->setTitle('дата выработки');
        $value->setDataType('timestamp');
        $value->setRangeType('continuous');
        $handler = new AllThings\Essence\AttributeManager($value,
            $linkToData);

        $isSuccess = $handler->correct($code);
        $this->assertTrue($isSuccess,
            'Attribute `' . $code . '`must be updated with success');

        $code = $context['place-of-production'];
        $value = Attribute::GetDefaultAttribute();
        $value->setCode($code);
        $value->setTitle('Место производства');
        $value->setDataType('symbol');
        $value->setRangeType('discrete');
        $handler = new AllThings\Essence\AttributeManager($value,
            $linkToData);

        $isSuccess = $handler->correct($code);
        $this->assertTrue($isSuccess,
            'Attribute `' . $code . '`must be updated with success');
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
        $linkToData = $context['PDO'];
        $essence = $context['essence'];

        $attributes = ['price', 'production-date', 'place-of-production'];
        foreach ($attributes as $attribute) {
            $this->LinkEssenceToAttribute($essence, $attribute,
                $linkToData);
        }

        return $context;
    }

    /**
     * @param $essence
     * @param $attribute
     * @param $linkToData
     */
    private function LinkEssenceToAttribute($essence, $attribute, $linkToData): void
    {
        $manager = new EssenceAttributeManager($essence, $attribute,
            $linkToData);

        $isSuccess = $manager->setUp();
        $this->assertTrue($isSuccess,
            "Attribute `$attribute` must be linked to"
            . " essence `$essence` with success");
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
        $linkToData = $context['PDO'];
        $essence = $context['essence'];
        $attributes = $this->getEssenceAttributes($essence, $linkToData);

        $codes = ['bun-with-jam', 'bun-with-raisins', 'cinnamon-bun'];
        foreach ($codes as $thing) {
            $context[$thing] = $thing;
            $this->createThing($thing, $linkToData);

            foreach ($attributes as $attribute) {
                $this->defineThing($thing, $attribute, $linkToData);;
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
    private function getEssenceAttributes(string $essence,
                                          PDO $linkToData): array
    {
        $manager = new EssenceAttributeManager($essence, '', $linkToData);
        $isSuccess = $manager->getAssociated();
        $this->assertTrue($isSuccess,
            "Attributes of essence `$essence`"
            . " must be fetched with success");
        if ($isSuccess) {
            $isSuccess = $manager->has();
            $this->assertTrue($isSuccess,
                "Essence `$essence` must be linked to some attributes");
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
        $this->assertTrue($isSuccess,
            'Thing `' . $code . '`must be created with success');
    }

    /**
     * @param $thing
     * @param $attribute
     * @param $linkToData
     */
    private function defineThing($thing, $attribute, $linkToData)
    {
        $content = (new Crossover())->setLeftValue($thing)->setRightValue($attribute);
        $handler = new ContentManager($content, $linkToData);

        $isSuccess = $handler->attach();
        $this->assertTrue($isSuccess,
            "Atribute `$attribute` must be defined for thing `$thing`"
            . 'with success');
    }

    /**
     * @param        $essence
     * @param string $code
     * @param        $linkToData
     *
     * @return bool
     */
    private function linkThingToEssence($essence, string $code, $linkToData): bool
    {
        $manager = new EssenceThingManager($essence, $code,
            $linkToData);
        $isSuccess = $manager->setUp();
        $this->assertTrue($isSuccess,
            "Thing `$code` must be linked to essence `$essence`"
            . 'with success');
        return $isSuccess;
    }

    /**
     * @depends testThingsCreate
     *
     * @param array $context
     */
    public function testSetupThing(array $context)
    {
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
        $this->assertTrue($isSuccess,
            "Thing `$code` title must be updated with success");
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
        $linkToData = $context['PDO'];

        $thing = $context['bun-with-jam'];

        $attribute = $context['price'];
        $value = (new Crossover())->setContent('15.50')
            ->setLeftValue($thing)->setRightValue($attribute);
        $this->defineContent($thing, $attribute, $value, $linkToData);

        $attribute = $context['production-date'];
        $value = (new Crossover())->setContent('20180429T1356')
            ->setLeftValue($thing)->setRightValue($attribute);
        $this->defineContent($thing, $attribute, $value, $linkToData);

        $attribute = $context['place-of-production'];
        $value = (new Crossover())->setContent('Екатеринбург')
            ->setLeftValue($thing)->setRightValue($attribute);
        $this->defineContent($thing, $attribute, $value, $linkToData);

        $thing = $context['bun-with-raisins'];

        $attribute = $context['price'];
        $value = (new Crossover())->setContent('9.50')
            ->setLeftValue($thing)->setRightValue($attribute);
        $this->defineContent($thing, $attribute, $value, $linkToData);

        $attribute = $context['production-date'];
        $value = (new Crossover())->setContent('20180427')
            ->setLeftValue($thing)->setRightValue($attribute);
        $this->defineContent($thing, $attribute, $value, $linkToData);

        $attribute = $context['place-of-production'];
        $value = (new Crossover())->setContent('Екатеринбург')
            ->setLeftValue($thing)->setRightValue($attribute);
        $this->defineContent($thing, $attribute, $value, $linkToData);

        $thing = $context['cinnamon-bun'];

        $attribute = $context['price'];
        $value = (new Crossover())->setContent('4.50')
            ->setLeftValue($thing)->setRightValue($attribute);
        $this->defineContent($thing, $attribute, $value, $linkToData);

        $attribute = $context['production-date'];
        $value = (new Crossover())->setContent('20180429')
            ->setLeftValue($thing)->setRightValue($attribute);
        $this->defineContent($thing, $attribute, $value, $linkToData);

        $attribute = $context['place-of-production'];
        $value = (new Crossover())->setContent('Челябинск')
            ->setLeftValue($thing)->setRightValue($attribute);
        $this->defineContent($thing, $attribute, $value, $linkToData);

        return $context;
    }

    /**
     * @param string     $thing
     * @param string     $attribute
     * @param ICrossover $value
     * @param PDO        $linkToData
     */
    private function defineContent(
        string $thing, string $attribute,
        ICrossover $value, PDO $linkToData): void
    {
        $handler = new ContentManager($value, $linkToData);
        $isSuccess = $handler->store($value);
        $this->assertTrue($isSuccess,
            "Attribute `$attribute` of thing `$thing`"
            . ' must be defined with success');
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
        $this->assertTrue($isSuccess,
            'Transaction must be rolled back');
    }
}
