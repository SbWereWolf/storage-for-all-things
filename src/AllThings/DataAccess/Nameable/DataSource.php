<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Nameable;

use Exception;
use PDO;

class DataSource implements ValuableReader
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

    /**
     * @throws Exception
     */
    public function select(): Nameable
    {
        $sqlText = "
select \"$this->uniqueIndex\",title,remark 
from $this->tableName
where \"$this->uniqueIndex\"=:target
ORDER BY \"$this->uniqueIndex\"
";

        $query = $this->db->prepare($sqlText);
        $query->bindParam(':target', $this->uniqueness);
        $result = $query->execute();

        $data = null;
        $isSuccess = $result === true;
        if ($isSuccess) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        }

        $isSuccess = $data !== false;
        if (!$isSuccess) {
            throw new Exception(
                "Fail read data for index `$this->uniqueness`"
            );
        }
        $row = $data[0];

        $code = $row[$this->uniqueIndex];
        $title = $row['title'];
        $remark = $row['remark'];

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $entity = (new NamedFactory())
            ->setCode($code)
            ->setTitle($title)
            ->setRemark($remark)
            ->makeNameable();

        return $entity;
    }
}
