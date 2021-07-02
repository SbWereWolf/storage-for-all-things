<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\Blueprint\Specification;


use AllThings\DataAccess\Crossover\ForeignKey;
use AllThings\DataAccess\Crossover\Linkable;
use AllThings\DataAccess\Retrievable;
use PDO;

class SpecificationHandler implements Linkable, Retrievable
{

    private $essenceForeignKey = null;
    private $attributeForeignKey = null;
    private $dataPath = null;
    private $dataSet = [];

    public function __construct(PDO $dataPath)
    {
        $this->essenceForeignKey = new ForeignKey('essence', 'id', 'code');
        $this->attributeForeignKey = new ForeignKey('attribute', 'id', 'code');

        $this->dataPath = $dataPath;
    }

    public function add(array $linkage): bool
    {
        $storageLocation = $this->getStorageLocation();

        $result = $storageLocation->insert($linkage);

        return $result;
    }

    /**
     * @return SpecificationLocation
     */
    private function getStorageLocation(): SpecificationLocation
    {
        $storageLocation = new SpecificationLocation(
            $this->essenceForeignKey,
            $this->attributeForeignKey,
            $this->dataPath
        );

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
     * @return SpecificationSource
     */
    private function getDataSource(): SpecificationSource
    {
        $dataSource = new SpecificationSource($this->essenceForeignKey, $this->attributeForeignKey, $this->dataPath);

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
