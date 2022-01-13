<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 9:02
 */

namespace AllThings\DataAccess\Uniquable;

use PDO;

class UniqueLocation implements UniquableWriter
{
    protected string $tableName;
    protected PDO $db;

    /**
     * @param string $table
     * @param PDO    $db
     */
    public function __construct(string $table, PDO $db)
    {
        $this->tableName = $table;
        $this->db = $db;
    }

    public function insert(string $uniqueness): bool
    {
        $connection = $this->db;

        $query = $connection->prepare(
            "insert into $this->tableName(code)values(:code)"
        );
        $query->bindParam(':code', $uniqueness);

        $result = $query->execute();

        return $result;
    }

    public function delete(string $uniqueness): bool
    {
        $connection = $this->db;

        $query = $connection->prepare(
            "DELETE from $this->tableName where code = :code"
        );
        $query->bindParam(':code', $uniqueness);

        $result = $query->execute();

        return $result;
    }
}
