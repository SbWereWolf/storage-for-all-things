<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 3:02
 */

namespace AllThings\DataAccess\Nameable;

use PDO;

class DataSource implements ValuableReader
{

    private string $tableName;
    /**
     * @var PDO
     */
    private PDO $dataSource;

    /**
     * @param string $table
     * @param PDO $dataSource
     */
    public function __construct(string $table, PDO $dataSource)
    {
        $this->tableName = $table;
        $this->dataSource = $dataSource;
    }

    public function select(Nameable $entity): bool
    {
        $target = $entity->getCode();

        $sqlText = "
select code,title,remark 
from $this->tableName
where code=:target
";
        $connection = $this->dataSource;

        $query = $connection->prepare($sqlText);
        $query->bindParam(':target', $target);
        $result = $query->execute();

        $data = null;
        $isSuccess = $result === true;
        if ($isSuccess) {
            $data = $query->fetchAll();
        }

        $isSuccess = !empty($data);
        if (!$isSuccess) {
            $result = false;
        }
        if ($isSuccess) {
            $row = $data[0];

            $code = $row['code'];
            $title = $row['title'];
            $remark = $row['remark'];

            $entity->setCode($code);
            $entity->setTitle($title);
            $entity->setRemark($remark);
        }

        return $result;
    }
}
