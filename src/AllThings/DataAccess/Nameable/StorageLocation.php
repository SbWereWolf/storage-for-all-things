<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\DataAccess\Nameable;

use AllThings\DataAccess\Uniquable\UniquableWriter;
use AllThings\DataAccess\Uniquable\UniqueLocation;

class StorageLocation
    extends UniqueLocation
    implements ValuableWriter,
               UniquableWriter
{
    public function update(
        Nameable $target_entity,
        Nameable $suggestion_entity,
    ): bool {
        $targetCode = $target_entity->getCode();
        $proposalCode = $suggestion_entity->getCode();
        $proposalTitle = $suggestion_entity->getTitle();
        $proposalRemark = $suggestion_entity->getRemark();

        $letUpdateCode = $targetCode !== $proposalCode;
        $updateCode = '';
        if ($letUpdateCode) {
            $updateCode = 'code = :proposalCode,';
        }

        $sqlText = "
update {$this->tableName} 
set 
    $updateCode
    title = :proposalTitle,
    remark=:proposalRemark
where code=:targetCode
";
        $connection = $this->storageLocation;
        $query = $connection->prepare($sqlText);

        if ($letUpdateCode) {
            $query->bindParam(':proposalCode', $proposalCode);
        }
        $query->bindParam(':proposalTitle', $proposalTitle);
        $query->bindParam(':proposalRemark', $proposalRemark);
        $query->bindParam(':targetCode', $targetCode);

        $result = $query->execute();

        return $result;
    }
}
