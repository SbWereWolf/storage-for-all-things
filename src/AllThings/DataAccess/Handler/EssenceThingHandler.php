<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 21:39
 */

namespace AllThings\DataAccess\Handler;


use AllThings\DataAccess\Implementation\EssenceThingLocation;
use AllThings\DataAccess\Implementation\EssenceThingSource;
use AllThings\DataObject\ForeignKey;
use PDO;

class EssenceThingHandler implements Linkable, Retrievable
{

    private $essenceForeignKey = null;
    private $thingForeignKey = null;
    private $dataPath = null;
    private $dataSet = [];

    public function __construct(PDO $dataPath)
    {
        $this->essenceForeignKey = new ForeignKey('essence', 'id', 'code');
        $this->thingForeignKey = new ForeignKey('thing', 'id', 'code');

        $this->dataPath = $dataPath;
    }

    function add(array $linkage): bool
    {
        $storageLocation = $this->getStorageLocation();

        $result = $storageLocation->insert($linkage);

        return $result;
    }

    /**
     * @return EssenceThingLocation
     */
    private function getStorageLocation(): EssenceThingLocation
    {
        $storageLocation = new EssenceThingLocation($this->essenceForeignKey, $this->thingForeignKey, $this->dataPath);

        return $storageLocation;
    }

    function remove(array $linkage): bool
    {
        $storageLocation = $this->getStorageLocation();

        $result = $storageLocation->delete($linkage);

        return $result;
    }

    function getRelated(array $linkage): bool
    {
        $dataSource = $this->getDataSource();

        $result = $dataSource->select($linkage);

        $isSuccess = $result === true;
        if ($isSuccess) {
            $this->dataSet = $dataSource->retrieveData();
        }

        return $result;
    }

    /**
     * @return EssenceThingSource
     */
    private function getDataSource(): EssenceThingSource
    {
        $dataSource = new EssenceThingSource($this->essenceForeignKey, $this->thingForeignKey, $this->dataPath);

        return $dataSource;
    }

    function retrieveData(): array
    {
        $result = $this->dataSet;

        return $result;
    }
}
