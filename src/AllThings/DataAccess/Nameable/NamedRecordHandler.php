<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\DataAccess\Nameable;


use AllThings\DataAccess\Retrievable;
use AllThings\DataAccess\Uniquable\UniqueHandler;
use PDO;

class NamedRecordHandler extends UniqueHandler implements Valuable, Retrievable
{
    private string $source;
    private ?Nameable $container;

    public function __construct(
        string $uniqueness,
        string $locationName,
        PDO $pdo,
    ) {
        parent::__construct($uniqueness, $locationName, $pdo);
        $this->source = $locationName;
    }

    private function getStorageLocation(): StorageLocation
    {
        $repository = new StorageLocation(
            $this->location,
            $this->dataPath,
        );
        return $repository;
    }

    public function write(string $code): bool
    {
        $entity = $this->container->getNameableCopy();
        $target = $entity;
        if ($code) {
            $target = (new NamedEntity())->setCode($code);
        }

        $result = ($this->getStorageLocation())
            ->update($target, $entity);

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
        $repository = new DataSource($this->source, $this->dataPath);
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

    /**
     * @param Nameable|null $container
     */
    public function setContainer(?Nameable $container): void
    {
        $this->container = $container;
    }

    /**
     * @param string $source
     */
    public function setSource(string $source): void
    {
        $this->source = $source;
    }
}
