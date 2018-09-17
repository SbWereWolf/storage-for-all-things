<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 0:44
 */

namespace AllThings\DataAccess\Implementation;


use AllThings\DataAccess\Core\AttributeWriter;
use AllThings\Essence\IAttribute;

class AttributeLocation implements AttributeWriter
{

    private $tableName = '';
    private $storageLocation = null;

    function __construct(string $table, \PDO $storageLocation)
    {

        $this->tableName = $table;
        $this->storageLocation = $storageLocation;
    }

    function insert(IAttribute $entity): bool
    {
        $suggestion_code = $entity->getCode();

        $sqlText = 'insert into ' . $this->tableName . ' (code)values(:code)';
        $connection = $this->storageLocation;

        $connection->beginTransaction();
        $query = $connection->prepare($sqlText);
        $query->bindParam(':code', $suggestion_code);

        $result = $this->executeQuery($query, $connection);

        return $result;
    }

    /**
     * @param $query
     * @param $connection
     * @return mixed
     */
    private function executeQuery(\PDOStatement $query, \PDO $connection)
    {
        $result = $query->execute();

        $isSuccess = $result === true;
        if ($isSuccess) {
            $result = $connection->commit();
        }
        if (!$isSuccess) {
            $connection->rollBack();
        }
        return $result;
    }

    function setIsHidden(IAttribute $entity): bool
    {
        $target_code = $entity->getCode();

        $sqlText = 'update ' . $this->tableName . ' set is_hidden = 1 where code = :code';
        $connection = $this->storageLocation;

        $connection->beginTransaction();
        $query = $connection->prepare($sqlText);
        $query->bindParam(':code', $target_code);

        $result = $this->executeQuery($query, $connection);

        return $result;
    }

    function update(IAttribute $target_entity, IAttribute $suggestion_entity): bool
    {
        $target_code = $target_entity->getCode();
        $suggestionCode = $suggestion_entity->getCode();
        $suggestionTitle = $suggestion_entity->getTitle();
        $suggestionRemark = $suggestion_entity->getRemark();
        $suggestionDataType = $suggestion_entity->getDataType();
        $suggestionRangeType = $suggestion_entity->getRangeType();

        $sqlText = 'update '
            . $this->tableName
            . ' set code=:suggestion_code,title=:suggestion_title,remark=:suggestion_remark,'
            . ' data_type=:suggestion_data_type,range_type=:suggestion_range_type '
            . ' where code=:target_code';
        $connection = $this->storageLocation;

        $connection->beginTransaction();
        $query = $connection->prepare($sqlText);
        $query->bindParam(':suggestion_code', $suggestionCode);
        $query->bindParam(':suggestion_title', $suggestionTitle);
        $query->bindParam(':suggestion_remark', $suggestionRemark);
        $query->bindParam(':suggestion_data_type', $suggestionDataType);
        $query->bindParam(':suggestion_range_type', $suggestionRangeType);
        $query->bindParam(':target_code', $target_code);

        $result = $this->executeQuery($query, $connection);

        return $result;
    }
}