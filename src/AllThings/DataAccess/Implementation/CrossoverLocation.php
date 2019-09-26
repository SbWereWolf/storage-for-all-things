<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 02.06.18 19:02
 */

namespace AllThings\DataAccess\Implementation;


use AllThings\DataAccess\Core\CrossoverWriter;
use AllThings\DataObject\ICrossover;
use AllThings\DataObject\ICrossoverTable;
use AllThings\DataObject\IForeignKey;
use PDO;

class CrossoverLocation implements CrossoverWriter
{

    private $tableStructure = null;
    private $dataPath = null;
    private $rightKey = null;
    private $leftKey = null;

    function __construct(IForeignKey $leftKey, IForeignKey $rightKey, ICrossoverTable $tableStructure, PDO $dataPath)
    {
        $this->tableStructure = $tableStructure;
        $this->dataPath = $dataPath;
        $this->rightKey = $rightKey;
        $this->leftKey = $leftKey;
    }

    function insert(ICrossover $entity): bool
    {
        $suggestionRightValue = $entity->getRightValue();
        $suggestionLeftValue = $entity->getLeftValue();

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
        $query->bindParam(':left_value', $suggestionLeftValue);
        $query->bindParam(':right_value', $suggestionRightValue);
        $result = $query->execute();

        return $result;
    }

    function update(ICrossover $targetEntity, ICrossover $suggestionEntity): bool
    {
        $targetRightValue = $targetEntity->getRightValue();
        $targetLeftValue = $targetEntity->getLeftValue();

        $suggestionRightValue = $suggestionEntity->getRightValue();
        $suggestionLeftValue = $suggestionEntity->getLeftValue();
        $suggestionContent = $suggestionEntity->getContent();

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
UPDATE $tableName
SET 
$leftColumn = (select $leftKeyColumn from $leftKeyTable where $leftKeyIndex = :suggestion_left),
$rightColumn = (select $rightKeyColumn from $rightKeyTable where $rightKeyIndex = :suggestion_right),
content = :content
WHERE 
    $leftColumn = (select $leftKeyColumn from $leftKeyTable where $leftKeyIndex = :target_left)
AND $rightColumn = (select $rightKeyColumn from $rightKeyTable where $rightKeyIndex = :target_right)
";
        $connection = $this->dataPath;
        $query = $connection->prepare($sqlText);
        $query->bindParam(':content', $suggestionContent);
        $query->bindParam(':suggestion_left', $suggestionLeftValue);
        $query->bindParam(':suggestion_right', $suggestionRightValue);
        $query->bindParam(':target_left', $targetLeftValue);
        $query->bindParam(':target_right', $targetRightValue);
        $result = $query->execute();

        return $result;
    }
}
