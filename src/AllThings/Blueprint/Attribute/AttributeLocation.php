<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\Blueprint\Attribute;

use AllThings\DataAccess\Uniquable\UniqueLocation;

class AttributeLocation extends UniqueLocation implements AttributeWriter
{
    public function update(IAttribute $suggestion): bool
    {
        $sqlText = "
update $this->tableName 
set
    title=:title,
    remark=:remark,
    data_type=:data_type,
    range_type=:range_type
where 
      \"$this->uniqueIndex\"=:target
";

        $query = $this->db->prepare($sqlText);

        $target = $suggestion->getCode();
        $query->bindParam(':target', $target);

        $title = $suggestion->getTitle();
        $query->bindParam(':title', $title);

        $remark = $suggestion->getRemark();
        $query->bindParam(':remark', $remark);

        $dataType = $suggestion->getDataType();
        $query->bindParam(':data_type', $dataType);

        $rangeType = $suggestion->getRangeType();
        $query->bindParam(':range_type', $rangeType);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $query->execute();

        return $result;
    }
}
