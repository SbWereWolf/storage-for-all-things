<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:46
 */

namespace AllThings\Catalog;


use AllThings\DataAccess\Crossover\ForeignKey;
use AllThings\DataAccess\Crossover\ICrossover;
use AllThings\DataAccess\Crossover\Linkable;
use AllThings\DataAccess\Retrievable;
use PDO;

class CatalogHandler implements Linkable, Retrievable
{
    private $dataSet = [];
    private CatalogLocation $location;
    private CatalogSource $source;

    public function __construct(PDO $dataPath, ForeignKey $essence, ForeignKey $thing)
    {
        $this->location = new CatalogLocation($essence, $thing, $dataPath);
        $this->source = new CatalogSource($essence, $thing, $dataPath);
    }

    public function add(ICrossover $linkage): bool
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
        return $this->location;
    }

    public function remove(ICrossover $linkage): bool
    {
        $storageLocation = $this->getStorageLocation();

        $result = $storageLocation->delete($linkage);

        return $result;
    }

    public function getRelated(ICrossover $linkage): bool
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
        return $this->source;
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
