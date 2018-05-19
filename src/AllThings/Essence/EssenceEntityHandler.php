<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:20
 */

namespace AllThings\Essence;


use AllThings\DataAccess\Implementation\EssenceDriver;
use AllThings\DataObject\Nameable;

class EssenceEntityHandler implements EssenceHandler
{
    private $entity = null;
    private $dataPath = null;


    public function __construct(IEssence $essence, \PDO $dataPath)
    {
        $this->entity = $essence;
        $this->dataPath = $dataPath;
    }

    function create(string $code): bool
    {

        $driver = $this->getDriver();

        $result = $driver->insert($code);

        $this->loadEntity($driver);

        return $result;
    }

    function remove(string $code): bool
    {
        $driver = $this->getDriver();

        $result = $driver->hide($code);

        $this->loadEntity($driver);

        return $result;
    }

    function correct(string $code): bool
    {
        $driver = $this->getDriver();

        $result = $driver->write($code);

        $this->loadEntity($driver);

        return $result;
    }

    function browse(string $code): bool
    {
        $driver = $this->getDriver();

        $result = $driver->read($code);

        $this->loadEntity($driver);

        return $result;
    }

    /**
     * @return EssenceDriver
     */
    private function getDriver(): EssenceDriver
    {
        $named = $this->entity;
        $dataPath = $this->dataPath;
        $driver = new EssenceDriver($named, $dataPath);

        return $driver;
    }

    /**
     * @param $driver
     */
    private function loadEntity(EssenceDriver $driver): void
    {
        $this->entity = $driver->retrieveData();
    }

    function retrieveData() :IEssence
    {
        $nameable = $this->entity->getNameableCopy();
        $storable = $this->entity->getStorableCopy();

        $data = new EssenceEntity($nameable,$storable);

        return $data;
    }
}
