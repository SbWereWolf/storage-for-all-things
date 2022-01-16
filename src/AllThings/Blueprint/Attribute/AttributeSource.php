<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */


namespace AllThings\Blueprint\Attribute;


use Exception;
use PDO;

class AttributeSource implements AttributeReader
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
     * @throws Exception
     */
    public function select(): IAttribute
    {
        $sqlText = "
select \"$this->uniqueIndex\",title,remark,data_type,range_type 
from $this->tableName 
where \"$this->uniqueIndex\"=:target_code";

        $query = $this->db->prepare($sqlText);
        $query->bindParam(':target_code', $this->uniqueness);
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
        $dataType = $row['data_type'];
        $rangeType = $row['range_type'];

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $entity = (new AttributeFactory())
            ->setCode($code)
            ->setTitle($title)
            ->setRemark($remark)
            ->setDataType($dataType)
            ->setRangeType($rangeType)
            ->makeAttribute();

        return $entity;
    }
}
