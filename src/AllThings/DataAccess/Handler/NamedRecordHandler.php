<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 11:27
 */

namespace AllThings\DataAccess\Handler;


use AllThings\DataAccess\Implementation\DataSource;
use AllThings\DataAccess\Implementation\StorageLocation;
use AllThings\DataObject\Nameable;
use AllThings\DataObject\NamedEntity;
use PDO;

class NamedRecordHandler implements Valuable, Hideable, Retrievable
{

    private $dataPath = null;
    private $table = '';
    private $container = null;

    function __construct(Nameable $named, string $tableName, PDO $dataPath)
    {
        $this->container = $named->getNameableCopy();
        $this->dataPath = $dataPath;
        $this->table = $tableName;
    }

    function add(): bool
    {
        $entity = $this->container->getNameableCopy();

        $result = ($this->getStorageLocation())->insert($entity);

        if ($result) {
            $this->container = $entity;
        }

        return $result;

    }

    private function getStorageLocation(): StorageLocation
    {

        $repository = new StorageLocation($this->table, $this->dataPath);
        return $repository;
    }

    function hide(): bool
    {

        $entity = $this->container->getNameableCopy();

        $result = ($this->getStorageLocation())->setIsHidden($entity);

        return $result;

    }

    function write(string $code): bool
    {

        $entity = (new NamedEntity())->setCode($code);

        $result = ($this->getStorageLocation())->update($entity, $this->container);

        return $result;

    }

    function read(): bool
    {

        $entity = (new NamedEntity())->setCode($code);

        $result = ($this->getDataSource())->select($entity);

        if ($result) {

            $this->container = $entity->getNameableCopy();
        }

        return $result;

    }

    private function getDataSource(): DataSource
    {

        $repository = new DataSource($this->table, $this->dataPath);
        return $repository;
    }

    function retrieveData(): Nameable
    {
        $data = $this->container->getNameableCopy();

        return $data;
    }
}
