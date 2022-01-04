<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\Blueprint\Attribute;

use AllThings\DataAccess\Uniquable\UniqueLocation;

class AttributeLocation extends UniqueLocation implements AttributeWriter
{
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
