<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 9:02
 */

namespace AllThings\Blueprint\Attribute;

use AllThings\DataAccess\Uniquable\UniqueLocation;

class AttributeLocation extends UniqueLocation implements AttributeWriter
{
    public function update(
        IAttribute $suggestion,
        string $target
    ): bool {
        if (!$target) {
            $target = $suggestion->getCode();
        }

        $code = $suggestion->getCode();
        $title = $suggestion->getTitle();
        $remark = $suggestion->getRemark();
        $dataType = $suggestion->getDataType();
        $rangeType = $suggestion->getRangeType();

        $letUpdateCode = $target !== $code;
        $updateCode = '';
        if ($letUpdateCode) {
            $updateCode = 'code = :code,';
        }

        $sqlText = "
update $this->tableName 
set
    $updateCode
    title=:title,
    remark=:remark,
    data_type=:data_type,
    range_type=:range_type
where 
      code=:target
";

        $query = $this->db->prepare($sqlText);

        if ($letUpdateCode) {
            $query->bindParam(':code', $code);
        }
        $query->bindParam(':title', $title);
        $query->bindParam(':remark', $remark);
        $query->bindParam(':data_type', $dataType);
        $query->bindParam(':range_type', $rangeType);
        $query->bindParam(':target', $target);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $query->execute();

        return $result;
    }
}
