<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 26.09.2019, 18:07
 */

use AllThings\Essence\Attribute;
use Environment\DbConnection;
use PHPUnit\Framework\TestCase;

class AttributeCrud extends TestCase
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
     */
    public function testAttributeCreate(array $context)
    {
        $code = 'price';
        $context['attribute'] = $code;

        $attribute = (Attribute::GetDefaultAttribute());
        $attribute->setCode($code);

        $linkToData = $context['PDO'];
        $handler = new AllThings\Essence\AttributeManager($attribute,
            $linkToData);

        $isSuccess = $handler->create();
        $this->assertTrue($isSuccess,
            'Attribute must be created with success');

        return $context;
    }

    /**
     * @depends testAttributeCreate
     *
     * @param array $context
     */
    public function testAttributeEdit(array $context)
    {
        $value = Attribute::GetDefaultAttribute();
        $value->setCode('price-rub');
        $value->setTitle('цена, руб.');
        $value->setRemark('Цена в рублях');
        $value->setDataType('decimal');
        $value->setRangeType('continuous');

        $linkToData = $context['PDO'];
        $handler = new AllThings\Essence\AttributeManager($value,
            $linkToData);

        $code = $context['attribute'];
        $isSuccess = $handler->correct($code);
        $this->assertTrue($isSuccess,
            'Attribute must be updated with success');

        $context['attribute'] = 'price-rub';

        return $context;
    }

    /**
     * @depends testAttributeEdit
     *
     * @param array $context
     */
    public function testAttributeDelete(array $context)
    {
        $target = Attribute::GetDefaultAttribute();
        $code = $context['attribute'];
        $target->setCode($code);

        $linkToData = $context['PDO'];
        $handler = new AllThings\Essence\attributeManager($target,
            $linkToData);

        $isSuccess = $handler->remove();

        $this->assertTrue($isSuccess,
            'attribute must be deleted with success');
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
