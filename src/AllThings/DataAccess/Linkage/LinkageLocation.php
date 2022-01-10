<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\DataAccess\Linkage;

use PDO;

class LinkageLocation implements LinkageWriter
{
    protected $tableStructure;
    protected $db;
    protected $rightKey;
    protected $leftKey;

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
        $connection = $this->db;
        $query = $connection->prepare($sqlText);
        $query->bindParam(':left_value', $proposalLeftValue);
        $query->bindParam(':right_value', $proposalRightValue);
        $result = $query->execute();

        return $result;
    }

    public function delete(ILinkage $entity): bool
    {
        $lTable = $this->leftKey->getTable();
        $lColumn = $this->leftKey->getColumn();
        $lIndex = $this->leftKey->getIndex();

        $sqlText = "
delete from {$this->tableStructure->getTableName()}
where 
{$this->tableStructure->getLeftColumn()}=
(select $lColumn from $lTable where $lIndex=:left)
";
        $right = $entity->getRightValue();
        if ($right) {
            $rTable = $this->rightKey->getTable();
            $rColumn = $this->rightKey->getColumn();
            $rIndex = $this->rightKey->getIndex();

            $sqlText .= "
AND {$this->tableStructure->getRightColumn()}
=(select $rColumn from $rTable where $rIndex=:right)
            ";
        }

        $query = $this->db->prepare($sqlText);

        $left = $entity->getLeftValue();
        $query->bindParam(':left', $left);

        if ($right) {
            $query->bindParam(':right', $right);
        }

        $result = $query->execute();

        return $result;
    }
}
