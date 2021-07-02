<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\Catalog;


use AllThings\DataAccess\Crossover\ForeignKey;
use AllThings\DataAccess\Crossover\Linkable;
use AllThings\DataAccess\Retrievable;
use PDO;

class CatalogHandler implements Linkable, Retrievable
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

    public function add(array $linkage): bool
    {
        $storageLocation = $this->getStorageLocation();

        $result = $storageLocation->insert($linkage);

        return $result;
    }

    /**
     * @return CatalogLocation
     */
    private function getStorageLocation(): CatalogLocation
    {
        $storageLocation = new CatalogLocation($this->essenceForeignKey, $this->thingForeignKey, $this->dataPath);

        return $storageLocation;
    }

    public function remove(array $linkage): bool
    {
        $storageLocation = $this->getStorageLocation();

        $result = $storageLocation->delete($linkage);

        return $result;
    }

    public function getRelated(array $linkage): bool
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
     * @return CatalogSource
     */
    private function getDataSource(): CatalogSource
    {
        $dataSource = new CatalogSource($this->essenceForeignKey, $this->thingForeignKey, $this->dataPath);

        return $dataSource;
    }

    public function retrieveData(): array
    {
        $result = $this->dataSet;

        return $result;
    }

    public function has(): bool
    {
        return !is_null($this->dataSet);
    }
}
