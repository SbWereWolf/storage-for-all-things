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
        IEssence $target_entity,
        IEssence $suggestion_entity
    ): bool {
        $target_code = $target_entity->getCode();
        $suggestion_code = $suggestion_entity->getCode();
        $suggestion_title = $suggestion_entity->getTitle();
        $suggestion_remark = $suggestion_entity->getRemark();
        $suggestion_storage = $suggestion_entity->getStorageKind();

        $sqlText = 'update '
            . $this->tableName
            . '
set 
    code=:suggestion_code,
    title=:suggestion_title,
    remark=:suggestion_remark,
    store_at=:suggestion_store_at 
where 
    code=:target_code';
        $connection = $this->storageLocation;

        $query = $connection->prepare($sqlText);
        $query->bindParam(
            ':suggestion_code',
            $suggestion_code
        );
        $query->bindParam(
            ':suggestion_title',
            $suggestion_title
        );
        $query->bindParam(
            ':suggestion_remark',
            $suggestion_remark
        );
        $query->bindParam(
            ':suggestion_store_at',
            $suggestion_storage
        );
        $query->bindParam(':target_code', $target_code);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $query->execute();

        return $result;
    }
}
