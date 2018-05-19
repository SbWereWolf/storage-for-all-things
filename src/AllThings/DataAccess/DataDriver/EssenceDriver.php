<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 20:27
 */

namespace AllThings\DataAccess\Implementation;


use AllThings\DataAccess\Core\DataSource;
use AllThings\DataAccess\Core\StorageLocation;
use AllThings\DataObject\NamedEntity;
use AllThings\Essence\EssenceEntity;
use AllThings\Essence\IEssence;

class EssenceDriver implements Valuable, Hideable, Retrievable
{

    private $storageLocation = 'essence';
    private $dataSource = 'essence';

    /**
     * @var \PDO
     */
    private $dataPath;
    private $container = null;

    function __construct(IEssence $entity, \PDO $dataPath)
    {
        $nameable = $entity->getNameableCopy();
        $storable = $entity->getStorableCopy();
        $essence = new EssenceEntity($nameable,$storable);

        $this->container = $essence;
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

        $repository = new StorageLocation($this->storageLocation, $this->dataPath);
        return $repository;
    }

    private function getDataSource(): DataSource
    {

        $repository = new DataSource($this->dataSource, $this->dataPath);
        return $repository;
    }

    function retrieveData():IEssence
    {
        $nameable = $this->container->getNameableCopy();
        $storable = $this->container->getStorableCopy();
        $essence = new EssenceEntity($nameable,$storable);

        return $essence;
    }
}
