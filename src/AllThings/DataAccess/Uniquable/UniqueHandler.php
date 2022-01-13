<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 9:02
 */

namespace AllThings\DataAccess\Uniquable;

use PDO;

class UniqueHandler implements Uniquable
{
    protected PDO $db;
    protected string $storageLocation;
    private string $uniqueness;
    private UniqueLocation $driver;

    /**
     * @param string $uniqueness
     * @param string $locationName
     * @param PDO    $db
     */
    public function __construct(
        string $uniqueness,
        string $locationName,
        PDO $db,
    ) {
        $this->uniqueness = $uniqueness;
        $this->db = $db;
        $this->storageLocation = $locationName;

        $this->driver = new UniqueLocation(
            $this->storageLocation,
            $this->db,
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