<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */


namespace AllThings\Blueprint\Attribute;


use PDO;

class AttributeSource implements AttributeReader
{

    private $tableName = '';
    /**
     * @var PDO
     */
    private $dataSource;

    public function __construct(string $table, PDO $dataSource)
    {
        $this->tableName = $table;
        $this->dataSource = $dataSource;
    }

    public function select(IAttribute $entity): bool
    {
        $targetCode = $entity->getCode();

        $sqlText = 'select code,title,remark,data_type,range_type from '
            . $this->tableName
            . ' where code=:target_code';
        $connection = $this->dataSource;

        $query = $connection->prepare($sqlText);
        $query->bindParam(':target_code', $targetCode);
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
            $dataType = $row['data_type'];
            $rangeType = $row['range_type'];

            $entity->setCode($code);
            $entity->setTitle($title);
            $entity->setRemark($remark);
            $entity->setDataType($dataType);
            $entity->setRangeType($rangeType);
        }

        return $result;
    }
}
