<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 25.09.2019, 23:06
 */

use AllThings\Essence\Essence;
use Environment\DbConnection;
use PHPUnit\Framework\TestCase;

class BusinessProcess extends TestCase
{

    const SKIP = false;
    /**
     * @return PDO
     */
    public function testInit()
    {
        define('APPLICATION_ROOT', realpath(__DIR__)
            . DIRECTORY_SEPARATOR . '..');

        /*require APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'vendor'
            . DIRECTORY_SEPARATOR . 'autoload.php';*/

        define('CONFIGURATION_ROOT', APPLICATION_ROOT
            . DIRECTORY_SEPARATOR . 'configuration');
        define('DB_READ_CONFIGURATION', CONFIGURATION_ROOT
            . DIRECTORY_SEPARATOR . 'db_test.php');

        $linkToData = (new DbConnection())->getForRead();

        $isSuccess = static::SKIP;
        if (!static::SKIP) {
            $isSuccess = $linkToData->beginTransaction();
        }
        $this->assertTrue($isSuccess,'Transaction must be opened');

        return $linkToData;
    }

    /**
     * @depends testInit
     *
     * @param PDO $linkToData
     */
    public function testEssenceCreate(PDO $linkToData){

        $essence = Essence::GetDefaultEssence();
        $essence->setCode('cake');
        $handler = new AllThings\Essence\EssenceManager($essence,
            $linkToData);

        $isSuccess = $handler->create();
        $this->assertTrue($isSuccess,
            'Essence must be created with success');

        $result = [];
        $result['essence'] = 'cake';
        $result['PDO'] = $linkToData;

        return $result;
    }

    /**
     * @depends testEssenceCreate
     *
     * @param array $context
     */
    public function testEssenceEdit(array $context)
    {
        $value = Essence::GetDefaultEssence();
        $value->setCode('cake2');
        $value->setTitle('The Cakes');
        $value->setRemark('Cakes  of all kinds');
        $value->setStoreAt('view');

        $linkToData = $context['PDO'];
        $handler = new AllThings\Essence\EssenceManager($value,
            $linkToData);

        $code = $context['essence'];
        $isSuccess = $handler->correct($code);

        $this->assertTrue($isSuccess,
            'Essence must be updated with success');

        $context['essence'] = 'cake2';

        return $context;
    }

    /**
     * @depends testEssenceEdit
     *
     * @param array $context
     */
    public function testEssenceShow(array $context)
    {
        $value = Essence::GetDefaultEssence();
        $code = $context['essence'];
        $value->setCode($code);

        $linkToData = $context['PDO'];
        $handler = new AllThings\Essence\EssenceManager($value,
            $linkToData);

        $isSuccess = $handler->browse();

        $this->assertTrue($isSuccess,
            'Essence must be readed with success');

        $content = $handler->retrieveData();

        $this->assertEquals($content->getCode(), $code,
            'Code must has value ' . $code);
        $this->assertEquals($content->getTitle(), 'The Cakes',
            'Title must has value ' . 'The Cakes');
        $this->assertEquals($content->getRemark(), 'Cakes  of all kinds',
            'Remark must has value ' . 'Cakes  of all kinds');
        $this->assertEquals($content->getStoreAt(), 'view',
            'StoreAt must has value ' . 'view');
    }

    /**
     * @depends testEssenceEdit
     *
     * @param array $context
     */
    public function testEssenceDelete(array $context)
    {
        $target = Essence::GetDefaultEssence();
        $code = $context['essence'];
        $target->setCode($code);

        $linkToData = $context['PDO'];
        $handler = new AllThings\Essence\EssenceManager($target,
            $linkToData);

        $isSuccess = $handler->remove();

        $this->assertTrue($isSuccess,
            'Essence must be deleted with success');
    }

    /**
     * @depends testInit
     *
     * @param PDO $linkToData
     */
    public function testFinally(PDO $linkToData)
    {
        $isSuccess = static::SKIP;
        if (!static::SKIP) {
            $isSuccess = $linkToData->rollBack();
        }
        $this->assertTrue($isSuccess,
            'Transaction must be rolled back');
    }

}
