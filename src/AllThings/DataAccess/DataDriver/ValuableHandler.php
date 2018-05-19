<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 11:27
 */

namespace AllThings\DataAccess\Implementation;


use AllThings\DataAccess\Core\DataSource;
use AllThings\DataAccess\Core\StorageLocation;
use AllThings\DataObject\Duplicable;
use AllThings\DataObject\Nameable;
use AllThings\DataObject\NameableDuplicable;
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

        $result = ($this->getStorageLocation())->addNamed($entity);

        if ($result) {
            $this->container = $entity;
        }

        return $result;

    }

    function hide(string $code): bool
    {

        $entity = (new NamedEntity())->setCode($code);

        $result = ($this->getStorageLocation())->hideNamed($entity);

        return $result;

    }

    function write(string $code): bool
    {

        $entity = (new NamedEntity())->setCode($code);

        $result = ($this->getStorageLocation())->writeNamed($entity, $this->container);

        return $result;

    }

    function read(string $code): bool
    {

        $entity = (new NamedEntity())->setCode($code);

        $result = ($this->getDataSource())->readNamed($entity);

        if ($result) {

            $this->container = $entity->getNameableCopy();
        }

        return $result;

    }

    private function getStorageLocation(): StorageLocation
    {

        $repository = new StorageLocation($this->storageLocation,$this->dataPath);
        return $repository;
    }

    private function getDataSource(): DataSource
    {

        $repository = new DataSource($this->storageLocation,$this->dataPath);
        return $repository;
    }

    function retrieveData(): Nameable
    {
        $data = $this->container->getNameableCopy();

        return $data;
    }
}
