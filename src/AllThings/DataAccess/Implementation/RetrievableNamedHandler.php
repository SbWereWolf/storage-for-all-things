<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 11:27
 */

namespace AllThings\DataAccess\Implementation;


use AllThings\DataAccess\Core\DataSource;
use AllThings\DataAccess\Core\StorageLocation;
use AllThings\DataObject\Named;
use AllThings\DataObject\NamedEntity;

class RetrievableNamedHandler implements Valuable, Hideable, Retrievable
{

    function __construct(Named $named)
    {
        $this->container = $named;
    }

    private $storageLocation = '';
    private $container = null;


    function add(string $code): bool
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

            $this->container = $entity->getDuplicate();
        }

        return $result;

    }

    private function getStorageLocation(): StorageLocation
    {

        $repository = new StorageLocation($this->storageLocation);
        return $repository;
    }

    private function getDataSource(): DataSource
    {

        $repository = new DataSource($this->storageLocation);
        return $repository;
    }

    function getData(): Named
    {
        $data = $this->container->getDuplicate();

        return $data;
    }
}
