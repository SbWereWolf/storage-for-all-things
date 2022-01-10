<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
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

        $proposalRightValue = $suggestionEntity->getRightValue();
        $proposalLeftValue = $suggestionEntity->getLeftValue();
        $proposalContent = $suggestionEntity->getContent();

        $leftKeyTable = $this->leftKey->getTable();
        $leftKeyColumn = $this->leftKey->getColumn();
        $leftKeyIndex = $this->leftKey->getIndex();

        $rightKeyTable = $this->rightKey->getTable();
        $rightKeyColumn = $this->rightKey->getColumn();
        $rightKeyIndex = $this->rightKey->getIndex();

        $tableName = $this->tableStructure->getTableName();
        $leftColumn = $this->tableStructure->getLeftColumn();
        $rightColumn = $this->tableStructure->getRightColumn();

        $updateLeftKey = '';
        if ($proposalLeftValue !== $targetLeftValue) {
            $updateLeftKey =
                "$leftColumn = (select $leftKeyColumn "
                . "from $leftKeyTable"
                . " where $leftKeyIndex = :proposal_left),";
        }
        $updateRightKey = '';
        if ($proposalRightValue !== $targetRightValue) {
            $updateRightKey =
                "$rightColumn = (select $rightKeyColumn "
                . "from $rightKeyTable "
                . "where $rightKeyIndex = :proposal_right),";
        }

        $sqlText = "
UPDATE $tableName
SET 
$updateLeftKey
$updateRightKey
content = :content
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
        if ($updateLeftKey) {
            $query->bindParam(':proposal_left', $proposalLeftValue);
        }
        if ($updateRightKey) {
            $query->bindParam(':proposal_right', $proposalRightValue);
        }
        $query->bindParam(':target_left', $targetLeftValue);
        $query->bindParam(':target_right', $targetRightValue);
        $result = $query->execute();

        return $result;
    }
}
