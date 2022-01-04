<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
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
        $target_code = $entity->getCode();

        $sqlText = 'select code,title,remark from '
            . $this->tableName
            . ' where code=:target_code';
        $connection = $this->dataSource;

        $connection->beginTransaction();
        $query = $connection->prepare($sqlText);
        $query->bindParam(':target_code', $target_code);
        $result = $query->execute();

        $isSuccess = $result === true;
        if ($isSuccess) {
            $result = $connection->commit();
        }
        if (!$isSuccess) {
            $connection->rollBack();
        }

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
