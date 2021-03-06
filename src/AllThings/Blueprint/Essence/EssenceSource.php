<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 04.07.2021, 2:22
 */

namespace AllThings\Blueprint\Essence;


use PDO;

class EssenceSource implements EssenceReader
{

    private string $tableName;
    /**
     * @var PDO
     */
    private PDO $dataSource;

    public function __construct(string $table, PDO $dataSource)
    {
        $this->tableName = $table;
        $this->dataSource = $dataSource;
    }

    public function select(IEssence $entity): bool
    {
        $target_code = $entity->getCode();

        $sqlText = 'select code,title,remark,store_at from '
            . $this->tableName
            . ' where code=:target_code';
        $connection = $this->dataSource;

        $query = $connection->prepare($sqlText);
        $query->bindParam(':target_code', $target_code);
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
            $storeAt = $row['store_at'];

            $entity->setCode($code);
            $entity->setTitle($title);
            $entity->setRemark($remark);
            $entity->setStorageKind($storeAt);
        }

        return $result;
    }
}
