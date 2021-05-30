<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 3:45
 */

namespace AllThings\DataAccess\Handler;


use AllThings\DataAccess\Implementation\EssenceAttributeLocation;
use AllThings\DataAccess\Implementation\EssenceAttributeSource;
use AllThings\DataObject\ForeignKey;
use PDO;

class EssenceAttributeHandler implements Linkable, Retrievable
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
     * @return EssenceAttributeLocation
     */
    private function getStorageLocation(): EssenceAttributeLocation
    {
        $storageLocation = new EssenceAttributeLocation($this->essenceForeignKey, $this->attributeForeignKey, $this->dataPath);

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
     * @return EssenceAttributeSource
     */
    private function getDataSource(): EssenceAttributeSource
    {
        $dataSource = new EssenceAttributeSource($this->essenceForeignKey, $this->attributeForeignKey, $this->dataPath);

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
