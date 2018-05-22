<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 11:27
 */

namespace AllThings\DataAccess\Handler;


use AllThings\DataAccess\Implementation\StorageLocation;
use AllThings\DataObject\Nameable;
use AllThings\DataObject\NamedEntity;

class ValuableHandler implements Valuable, Hideable, Retrievable
{

    /**
     * @var \PDO
     */
    private $dataPath;
    private $storageLocation = '';
    private $container = null;

    function __construct(Nameable $named, \PDO $dataPath)
    {
        $this->container = $named->getNameableCopy();
        $this->dataPath = $dataPath;
    }

    function insert(string $code): bool
    {

        $entity = (new NamedEntity())->setCode($code);

        $result = ($this->getStorageLocation())->add($entity);

        if ($result) {
            $this->container = $entity;
        }

        return $result;

    }

    private function getStorageLocation(): StorageLocation
    {

        $repository = new StorageLocation($this->storageLocation, $this->dataPath);
        return $repository;
    }

    function hide(string $code): bool
    {

        $entity = (new NamedEntity())->setCode($code);

        $result = ($this->getStorageLocation())->hide($entity);

        return $result;

    }

    function write(string $code): bool
    {

        $entity = (new NamedEntity())->setCode($code);

        $result = ($this->getStorageLocation())->write($entity, $this->container);

        return $result;

    }

    function read(string $code): bool
    {

        $entity = (new NamedEntity())->setCode($code);

        $result = ($this->getDataSource())->read($entity);

        if ($result) {

            $this->container = $entity->getNameableCopy();
        }

        return $result;

    }

    private function getDataSource(): DataSource
    {

        $repository = new DataSource($this->storageLocation, $this->dataPath);
        return $repository;
    }

    function retrieveData(): Nameable
    {
        $data = $this->container->getNameableCopy();

        return $data;
    }
}
