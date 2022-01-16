<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Nameable;

use AllThings\DataAccess\Uniquable\UniquableWriter;
use AllThings\DataAccess\Uniquable\UniqueLocation;

class StorageLocation
    extends UniqueLocation
    implements ValuableWriter,
               UniquableWriter
{

    public function update(Nameable $suggestion): bool
    {
        $title = $suggestion->getTitle();
        $remark = $suggestion->getRemark();

        $sqlText = "
update $this->tableName 
set 
    title=:title,
    remark=:remark
where
      \"$this->uniqueIndex\"=:target
";
        $query = $this->db->prepare($sqlText);

        $query->bindParam(':title', $title);
        $query->bindParam(':remark', $remark);
        $query->bindParam(':target', $this->uniqueness);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $query->execute();

        return $result;
    }
}
