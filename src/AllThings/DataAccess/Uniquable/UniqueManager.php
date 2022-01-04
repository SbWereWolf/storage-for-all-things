<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\DataAccess\Uniquable;

use PDO;

class UniqueManager implements UniquableManager
{
    protected PDO $dataPath;
    protected string $storageLocation;
    private string $uniqueness;

    /**
     * @param string $uniqueness
     * @param string $storageLocation
     * @param PDO $dataPath
     */
    public function __construct(
        string $uniqueness,
        string $storageLocation,
        PDO $dataPath,
    ) {
        $this->uniqueness = $uniqueness;
        $this->dataPath = $dataPath;
        $this->storageLocation = $storageLocation;
    }

    public function create(): bool
    {
        $result = $this->getUniquableHandler()->add();

        return $result;
    }

    private function getUniquableHandler(): Uniquable
    {
        $handler = new UniqueHandler(
            $this->uniqueness,
            $this->storageLocation,
            $this->dataPath,
        );

        return $handler;
    }

    public function remove(): bool
    {
        $result = $this->getUniquableHandler()->erase();

        return $result;
    }
}
