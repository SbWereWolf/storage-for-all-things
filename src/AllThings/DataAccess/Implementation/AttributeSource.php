<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 0:35
 */


namespace AllThings\DataAccess\Implementation;


use AllThings\DataAccess\Core\AttributeReader;
use AllThings\Essence\IAttribute;

class AttributeSource implements AttributeReader
{

    private $tableName = '';
    /**
     * @var \PDO
     */
    private $dataSource;

    function __construct(string $table, \PDO $dataSource)
    {

        $this->tableName = $table;
        $this->dataSource = $dataSource;
    }

    function read(IAttribute $entity): bool
    {
        $targetCode = $entity->getCode();

        $sqlText = 'select code,title,remark,data_type,range_type from '
            . $this->tableName
            . ' where code=:target_code';
        $connection = $this->dataSource;

        $connection->beginTransaction();
        $query = $connection->prepare($sqlText);
        $query->bindParam(':target_code', $targetCode);
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
