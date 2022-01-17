<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 17.01.2022, 7:56
 */

namespace AllThings\DataAccess\Crossover;

use AllThings\DataAccess\Linkage\LinkageLocation;

class CrossoverLocation
    extends LinkageLocation
    implements CrossoverWriter
{
    public function update(
        ICrossover $targetEntity,
        ICrossover $suggestionEntity
    ): bool {
        $targetRightValue = $targetEntity->getRightValue();
        $targetLeftValue = $targetEntity->getLeftValue();
        $proposalContent = $suggestionEntity->getContent();

        $leftKeyTable = $this->leftKey->getTable();
        $leftKeyColumn = $this->leftKey->getPrimaryIndex();
        $leftKeyIndex = $this->leftKey->getMatchColumn();

        $rightKeyTable = $this->rightKey->getTable();
        $rightKeyColumn = $this->rightKey->getPrimaryIndex();
        $rightKeyIndex = $this->rightKey->getMatchColumn();

        $tableName = $this->tableStructure->getTableName();
        $leftColumn = $this->tableStructure->getLeftForeign();
        $rightColumn = $this->tableStructure->getRightForeign();

        $sqlText = "
UPDATE $tableName
SET content = :content
WHERE 
    $leftColumn = 
    (select $leftKeyColumn from $leftKeyTable 
    where $leftKeyIndex = :target_left)
AND $rightColumn = 
(select $rightKeyColumn from $rightKeyTable 
where $rightKeyIndex = :target_right)
";
        $connection = $this->db;
        $query = $connection->prepare($sqlText);
        $query->bindParam(':content', $proposalContent);
        $query->bindParam(':target_left', $targetLeftValue);
        $query->bindParam(':target_right', $targetRightValue);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $query->execute();

        return $result;
    }
}
