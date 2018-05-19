<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:20
 */

namespace AllThings\Essence;


use AllThings\DataAccess\Implementation\EssenceDriver;

class EssenceEntityHandler implements EssenceHandler
{
    private $subject = null;
    private $dataPath = null;


    public function __construct(IEssence $subject, \PDO $dataPath)
    {
        $this->subject = $subject;
        $this->dataPath = $dataPath;
    }

    function create(string $code): bool
    {

        $driver = $this->getDriver();

        $result = $driver->insert($code);

        $this->loadEntity($driver);

        return $result;
    }

    /**
     * @return EssenceDriver
     */
    private function getDriver(): EssenceDriver
    {
        $subject = $this->subject;
        $dataPath = $this->dataPath;
        $driver = new EssenceDriver($subject, $dataPath);

        return $driver;
    }

    /**
     * @param $driver
     */
    private function loadEntity(EssenceDriver $driver): void
    {
        $this->subject = $driver->retrieveData();
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

    function retrieveData(): IEssence
    {
        $nameable = $this->subject->getNameableCopy();
        $storable = $this->subject->getStorableCopy();

        $data = new EssenceEntity($nameable, $storable);

        return $data;
    }
}
