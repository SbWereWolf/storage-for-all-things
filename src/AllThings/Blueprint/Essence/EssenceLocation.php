<?php

/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\Blueprint\Essence;

use AllThings\DataAccess\Uniquable\UniqueLocation;

class EssenceLocation extends UniqueLocation implements EssenceWriter
{
    public function update(
        IEssence $suggestion,
        string $target = ''
    ): bool {
        if ($target) {
            $target_code = $target;
        }
        if (!$target) {
            $target_code = $suggestion->getCode();
        }

        $suggestion_code = $suggestion->getCode();
        $suggestion_title = $suggestion->getTitle();
        $suggestion_remark = $suggestion->getRemark();
        $suggestion_storage = $suggestion->getStorageKind();

        $sqlText = 'update '
            . $this->tableName
            . '
set 
    code=:code,
    title=:title,
    remark=:remark,
    store_at=:store_at 
where 
    code=:target_code';
        $connection = $this->storageLocation;

        $query = $connection->prepare($sqlText);
        $query->bindParam(
            ':code',
            $suggestion_code
        );
        $query->bindParam(
            ':title',
            $suggestion_title
        );
        $query->bindParam(
            ':remark',
            $suggestion_remark
        );
        $query->bindParam(
            ':store_at',
            $suggestion_storage
        );
        $query->bindParam(':target_code', $target_code);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $query->execute();

        return $result;
    }
}
