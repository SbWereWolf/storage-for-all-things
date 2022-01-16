<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Linkage;

use PDO;

class LinkageLocation implements LinkageWriter
{
    protected ILinkageTable $tableStructure;
    protected PDO $db;
    protected IForeignKey $rightKey;
    protected IForeignKey $leftKey;

    public function __construct(
        IForeignKey $leftKey,
        IForeignKey $rightKey,
        ILinkageTable $tableStructure,
        PDO $db
    ) {
        $this->tableStructure = $tableStructure;
        $this->db = $db;
        $this->rightKey = $rightKey;
        $this->leftKey = $leftKey;
    }

    public function insert(ILinkage $entity): bool
    {
        $proposalRightValue = $entity->getRightValue();
        $proposalLeftValue = $entity->getLeftValue();

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
insert into $tableName ($leftColumn,$rightColumn)
values((
select $leftKeyColumn from $leftKeyTable where $leftKeyIndex = :left_value
),(
select $rightKeyColumn from $rightKeyTable where $rightKeyIndex = :right_value
))";
        $connection = $this->db;
        $query = $connection->prepare($sqlText);
        $query->bindParam(':left_value', $proposalLeftValue);
        $query->bindParam(':right_value', $proposalRightValue);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $query->execute();

        return $result;
    }

    public function delete(ILinkage $entity): bool
    {
        $lTable = $this->leftKey->getTable();
        $lColumn = $this->leftKey->getPrimaryIndex();
        $lIndex = $this->leftKey->getMatchColumn();

        $sqlText = "
delete from {$this->tableStructure->getTableName()}
where 
{$this->tableStructure->getLeftForeign()}=
(select $lColumn from $lTable where $lIndex=:left)
";
        $right = $entity->getRightValue();
        if ($right) {
            $rTable = $this->rightKey->getTable();
            $rColumn = $this->rightKey->getPrimaryIndex();
            $rIndex = $this->rightKey->getMatchColumn();

            $sqlText .= "
AND {$this->tableStructure->getRightForeign()}
=(select $rColumn from $rTable where $rIndex=:right)
            ";
        }

        $query = $this->db->prepare($sqlText);

        $left = $entity->getLeftValue();
        $query->bindParam(':left', $left);

        if ($right) {
            $query->bindParam(':right', $right);
        }

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $query->execute();

        return $result;
    }
}
