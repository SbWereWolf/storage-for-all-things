<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */

namespace AllThings\DataAccess\Crossover;


use PDO;

class CrossoverLocation implements CrossoverWriter
{

    private $tableStructure;
    private $dataPath;
    private $rightKey;
    private $leftKey;

    public function __construct(
        IForeignKey $leftKey,
        IForeignKey $rightKey,
        ICrossoverTable $tableStructure,
        PDO $dataPath
    ) {
        $this->tableStructure = $tableStructure;
        $this->dataPath = $dataPath;
        $this->rightKey = $rightKey;
        $this->leftKey = $leftKey;
    }

    public function insert(ICrossover $entity): bool
    {
        $proposalRightValue = $entity->getRightValue();
        $proposalLeftValue = $entity->getLeftValue();

        $leftKeyTable = $this->leftKey->getTable();
        $leftKeyColumn = $this->leftKey->getColumn();
        $leftKeyIndex = $this->leftKey->getIndex();

        $rightKeyTable = $this->rightKey->getTable();
        $rightKeyColumn = $this->rightKey->getColumn();
        $rightKeyIndex = $this->rightKey->getIndex();

        $tableName = $this->tableStructure->getTableName();
        $leftColumn = $this->tableStructure->getLeftColumn();
        $rightColumn = $this->tableStructure->getRightColumn();

        $sqlText = "
insert into $tableName ($leftColumn,$rightColumn)
values((
select $leftKeyColumn from $leftKeyTable where $leftKeyIndex = :left_value
),(
select $rightKeyColumn from $rightKeyTable where $rightKeyIndex = :right_value
))";
        $connection = $this->dataPath;
        $query = $connection->prepare($sqlText);
        $query->bindParam(':left_value', $proposalLeftValue);
        $query->bindParam(':right_value', $proposalRightValue);
        $result = $query->execute();

        return $result;
    }

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
        $connection = $this->dataPath;
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
