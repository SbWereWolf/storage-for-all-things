<?php

/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\Blueprint\Essence;

use AllThings\DataAccess\Uniquable\UniqueLocation;

class EssenceLocation extends UniqueLocation implements EssenceWriter
{
    public function update(IEssence $suggestion): bool
    {
        $sqlText = "
update $this->tableName
set
    title=:title,
    remark=:remark,
    store_at=:store_at 
where 
    \"$this->uniqueIndex\"=:target
";
        $query = $this->db->prepare($sqlText);

        $code = $suggestion->getCode();
        $query->bindParam(':target', $code);

        $title = $suggestion->getTitle();
        $query->bindParam(':title', $title);

        $remark = $suggestion->getRemark();
        $query->bindParam(':remark', $remark);

        $storageKind = $suggestion->getStorageManner();
        $query->bindParam(':store_at', $storageKind);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $query->execute();

        return $result;
    }
}
