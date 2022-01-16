<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Uniquable;

use PDO;

class UniqueSource implements UniquableReader
{
    protected PDO $db;
    protected array $uniqueness;
    protected string $uniqueIndex;
    private string $tableName;

    /**
     * @param PDO    $db
     * @param string $table
     * @param array  $uniquenesses
     * @param string $uniqueIndex
     */
    public function __construct(
        PDO $db,
        string $table,
        array $uniquenesses,
        string $uniqueIndex
    ) {
        $this->db = $db;
        $this->tableName = $table;
        $this->uniqueness = $uniquenesses;
        $this->uniqueIndex = $uniqueIndex;
    }

    public function select(array $fields): array
    {
        $index = $this->uniqueIndex;
        if (!in_array($index, $fields)) {
            $fields[] = $index;
        }
        $copy = $this->uniqueness;
        array_walk($copy, function (&$val, $key) {
            $val = ":p$key";
        });

        $uniquenesses = implode(',', $copy);
        $columns = implode('","', $fields);
        $columns = "\"$columns\"";

        $sqlText =
            "
select $columns 
from $this->tableName 
where \"$index\" in ($uniquenesses)
";
        $query = $this->db->prepare($sqlText);

        foreach ($copy as $key => $val) {
            $query->bindParam(":p$key", $this->uniqueness[$key]);
        }

        $result = $query->execute() !== false;
        $data = $result ?
            $query->fetchAll(PDO::FETCH_ASSOC)
            : [];

        $data = array_column($data, null, $index);
        foreach ($data as $key => $val) {
            unset($data[$key][$index]);
        }

        return $data;
    }
}
