<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\Blueprint\Essence;


use Exception;
use PDO;

class EssenceSource implements EssenceReader
{
    protected string $tableName;
    protected PDO $db;
    protected string $uniqueness;
    protected string $uniqueIndex;

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
     * @return IEssence
     * @throws Exception
     */
    public function select(): IEssence
    {
        $sqlText = "
select \"$this->uniqueIndex\",title,remark,store_at 
from $this->tableName 
where \"$this->uniqueIndex\"=:target";

        $query = $this->db->prepare($sqlText);
        $query->bindParam(':target', $this->uniqueness);
        $isSuccess = $query->execute();

        $data = [];
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
        $storeAt = $row['store_at'];

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $entity = (new EssenceFactory())
            ->setCode($code)
            ->setTitle($title)
            ->setRemark($remark)
            ->setStorageManner($storeAt)
            ->makeEssence();

        return $entity;
    }
}
