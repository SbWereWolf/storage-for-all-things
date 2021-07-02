<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\DataAccess\Nameable;


use AllThings\DataAccess\Retrievable;
use PDO;

class NamedRecordHandler implements Valuable, Hideable, Retrievable
{

    private $dataPath = null;
    private $table = '';
    private $container = null;

    public function __construct(Nameable $named, string $tableName, PDO $dataPath)
    {
        $this->container = $named->getNameableCopy();
        $this->dataPath = $dataPath;
        $this->table = $tableName;
    }

    public function add(): bool
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

    public function hide(): bool
    {
        $entity = $this->container->getNameableCopy();

        $result = ($this->getStorageLocation())->setIsHidden($entity);

        return $result;
    }

    public function write(string $code): bool
    {
        $entity = (new NamedEntity())->setCode($code);

        $result = ($this->getStorageLocation())
            ->update($entity, $this->container);

        return $result;
    }

    public function read(): bool
    {
        $entity = (new NamedEntity())->setCode(
            $this->container->getCode()
        );

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

    public function retrieveData(): Nameable
    {
        $data = $this->container->getNameableCopy();

        return $data;
    }

    public function has(): bool
    {
        return !is_null($this->container);
    }
}
