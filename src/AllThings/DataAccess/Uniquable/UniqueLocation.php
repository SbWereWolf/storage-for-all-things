<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Uniquable;

use PDO;

class UniqueLocation implements UniquableWriter
{
    protected string $tableName;
    protected PDO $db;
    protected string $uniqueness;
    protected string $uniqueIndex;

    /**
     * @param PDO    $db
     * @param string $table
     * @param string $uniqueness
     * @param string $uniqueIndex
     */
    public function __construct(
        PDO $db,
        string $table,
        string $uniqueness,
        string $uniqueIndex = 'code'
    ) {
        $this->tableName = $table;
        $this->db = $db;
        $this->uniqueness = $uniqueness;
        $this->uniqueIndex = $uniqueIndex;
    }

    public function insert(): bool
    {
        $connection = $this->db;

        $query = $connection->prepare(
            "
insert into $this->tableName(\"$this->uniqueIndex\")values(:code)
"
        );
        $query->bindParam(':code', $this->uniqueness);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $query->execute();

        return $result;
    }

    public function delete(): bool
    {
        $connection = $this->db;

        $query = $connection->prepare(
            "
DELETE 
from $this->tableName 
where \"$this->uniqueIndex\" = :code
"
        );
        $query->bindParam(':code', $this->uniqueness);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $query->execute();

        return $result;
    }
}
