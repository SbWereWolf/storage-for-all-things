<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\DataAccess\Uniquable;

use PDO;

class UniqueHandler implements Uniquable
{
    protected PDO $dataPath;
    protected string $location;
    private string $uniqueness;
    private UniqueLocation $driver;

    /**
     * @param string $uniqueness
     * @param string $locationName
     * @param PDO $pdo
     */
    public function __construct(
        string $uniqueness,
        string $locationName,
        PDO $pdo,
    ) {
        $this->uniqueness = $uniqueness;
        $this->dataPath = $pdo;
        $this->location = $locationName;

        $this->driver = new UniqueLocation(
            $this->location,
            $this->dataPath,
        );
    }

    public function add(): bool
    {
        $result = $this->driver->insert($this->uniqueness);

        return $result;
    }

    public function erase(): bool
    {
        $result = $this->driver->delete($this->uniqueness);

        return $result;
    }
}