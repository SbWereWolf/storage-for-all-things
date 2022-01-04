<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\DataAccess\Uniquable;

use PDO;

class UniqueLocation implements UniquableWriter
{
    protected string $tableName;
    protected PDO $storageLocation;

    /**
     * @param string $table
     * @param PDO $storageLocation
     */
    public function __construct(string $table, PDO $storageLocation)
    {
        $this->tableName = $table;
        $this->storageLocation = $storageLocation;
    }

    public function insert(string $uniqueness): bool
    {
        $connection = $this->storageLocation;

        $query = $connection->prepare(
            "insert into $this->tableName(code)values(:code)"
        );
        $query->bindParam(':code', $uniqueness);

        $result = $query->execute();

        return $result;
    }

    public function delete(string $uniqueness): bool
    {
        $connection = $this->storageLocation;

        $query = $connection->prepare(
            "DELETE from $this->tableName where code = :code"
        );
        $query->bindParam(':code', $uniqueness);

        $result = $query->execute();

        return $result;
    }
}
