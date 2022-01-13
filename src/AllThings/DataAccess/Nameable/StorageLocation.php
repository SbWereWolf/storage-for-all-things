<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 9:02
 */

namespace AllThings\DataAccess\Nameable;

use AllThings\DataAccess\Uniquable\UniquableWriter;
use AllThings\DataAccess\Uniquable\UniqueLocation;

class StorageLocation
    extends UniqueLocation
    implements ValuableWriter,
               UniquableWriter
{
    public function update(Nameable $suggestion, string $target,): bool
    {
        if ($target) {
            $target = $suggestion->getCode();
        }
        $code = $suggestion->getCode();
        $title = $suggestion->getTitle();
        $remark = $suggestion->getRemark();

        $letUpdateCode = $target !== $code;
        $updateCode = '';
        if ($letUpdateCode) {
            $updateCode = 'code = :code,';
        }

        $sqlText = "
update {$this->tableName} 
set 
    $updateCode
    title=:title,
    remark=:remark
where
      code=:target
";
        $query = $this->db->prepare($sqlText);

        if ($letUpdateCode) {
            $query->bindParam(':code', $code);
        }
        $query->bindParam(':title', $title);
        $query->bindParam(':remark', $remark);
        $query->bindParam(':target', $targetCode);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $query->execute();

        return $result;
    }
}
