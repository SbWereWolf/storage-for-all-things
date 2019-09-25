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

        $isSuccess = $linkToData->beginTransaction();
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
        $handler = new AllThings\Essence\EssenceManager($essence, $linkToData);

        $isSuccess = $handler->create();
        $this->assertTrue($isSuccess,'Essence must be created');
    }

    /**
     * @depends testInit
     *
     * @param PDO $linkToData
     */
    public function testFinally(PDO $linkToData)
    {

        $isSuccess = $linkToData->rollBack();
        $this->assertTrue($isSuccess,'Transaction must be rolled back');
    }

}
