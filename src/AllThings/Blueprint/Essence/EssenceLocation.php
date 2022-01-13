<?php

/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 9:02
 */

namespace AllThings\Blueprint\Essence;

use AllThings\DataAccess\Uniquable\UniqueLocation;

class EssenceLocation extends UniqueLocation implements EssenceWriter
{
    public function update(
        IEssence $suggestion,
        string $target = ''
    ): bool {
        if (!$target) {
            $target = $suggestion->getCode();
        }

        $code = $suggestion->getCode();
        $title = $suggestion->getTitle();
        $remark = $suggestion->getRemark();
        $storageKind = $suggestion->getStorageKind();

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
    store_at=:store_at 
where 
    code=:target
";
        $query = $this->db->prepare($sqlText);

        if ($letUpdateCode) {
            $query->bindParam(':code', $code);
        }
        $query->bindParam(':title', $title);
        $query->bindParam(':remark', $remark);
        $query->bindParam(':store_at', $storageKind);
        $query->bindParam(':target', $target);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $query->execute();

        return $result;
    }
}
