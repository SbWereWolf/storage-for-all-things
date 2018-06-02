<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 02.06.18 22:32
 */

namespace AllThings\DataAccess\Implementation;


use AllThings\DataAccess\Core\CrossoverReader;
use AllThings\DataObject\ICrossover;
use AllThings\DataObject\ICrossoverTable;
use AllThings\DataObject\IForeignKey;

class CrossoverSource implements CrossoverReader
{

    private $tableStructure = null;
    private $dataPath = null;
    private $rightKey = null;
    private $leftKey = null;


    function __construct(IForeignKey $leftKey, IForeignKey $rightKey, ICrossoverTable $tableStructure, \PDO $dataPath)
    {
        $this->tableStructure = $tableStructure;
        $this->dataPath = $dataPath;
        $this->rightKey = $rightKey;
        $this->leftKey = $leftKey;
    }

    function select(ICrossover $entity): bool
    {
        $targetLeftValue = $entity->getLeftValue();
        $targetRightValue = $entity->getRightValue();

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
select content 
from $tableName
WHERE 
    $leftColumn = (select $leftKeyColumn from $leftKeyTable where $leftKeyIndex = :target_left)
AND $rightColumn = (select $rightKeyColumn from $rightKeyTable where $rightKeyIndex = :target_right)
";
        $connection = $this->dataPath;

        $connection->beginTransaction();
        $query = $connection->prepare($sqlText);
        $query->bindParam(':target_left', $targetLeftValue);
        $query->bindParam(':target_right', $targetRightValue);
        $result = $query->execute();

        $isSuccess = $result === true;
        if ($isSuccess) {
            $result = $connection->commit();
        }
        if (!$isSuccess) {
            $connection->rollBack();
        }

        $data = null;
        $isSuccess = $result === true;
        if ($isSuccess) {

            $data = $query->fetchAll();
        }

        $isSuccess = !empty($data);
        if (!$isSuccess) {
            $result = false;
        }
        if ($isSuccess) {

            $row = $data[0];

            $content = $row['content'];

            $entity->setContent($content);

        }

        return $result;
    }

}
