<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:44
 */

namespace AllThings\DataAccess\Implementation;


use AllThings\Attribute\IAttribute;
use AllThings\DataAccess\Core\AttributeWriter;
use PDO;

class AttributeLocation implements AttributeWriter
{

    private $tableName = '';
    private $storageLocation = null;

    public function __construct(string $table, PDO $storageLocation)
    {
        $this->tableName = $table;
        $this->storageLocation = $storageLocation;
    }

    public function insert(IAttribute $entity): bool
    {
        $suggestion_code = $entity->getCode();

        $sqlText = 'insert into ' . $this->tableName . ' (code)values(:code)';
        $connection = $this->storageLocation;

        $query = $connection->prepare($sqlText);
        $query->bindParam(':code', $suggestion_code);

        $result = $query->execute();

        return $result;
    }

    public function setIsHidden(IAttribute $entity): bool
    {
        $target_code = $entity->getCode();

        $sqlText = 'update ' . $this->tableName . ' set is_hidden = 1 where code = :code';
        $connection = $this->storageLocation;

        $query = $connection->prepare($sqlText);
        $query->bindParam(':code', $target_code);

        $result = $query->execute();

        return $result;
    }

    public function update(IAttribute $target_entity, IAttribute $suggestion_entity): bool
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

        $query = $connection->prepare($sqlText);
        $query->bindParam(':suggestion_code', $suggestionCode);
        $query->bindParam(':suggestion_title', $suggestionTitle);
        $query->bindParam(':suggestion_remark', $suggestionRemark);
        $query->bindParam(':suggestion_data_type', $suggestionDataType);
        $query->bindParam(':suggestion_range_type', $suggestionRangeType);
        $query->bindParam(':target_code', $target_code);

        $result = $query->execute();

        return $result;
    }
}
