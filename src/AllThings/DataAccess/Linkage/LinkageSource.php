<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 23.01.2022, 12:58
 */

namespace AllThings\DataAccess\Linkage;

use AllThings\DataAccess\Common\ColumnManager;
use PDO;

class LinkageSource implements RelatedReading
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

    public function getRelatedFields(
        ILinkage $linkage,
        string $field,
    ): array {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $this->getRelatedRecords($linkage, [$field]);

        return $result;
    }

    public function getRelatedRecords(
        ILinkage $linkage,
        array $fields
    ): array {
        $rColumn = $this->rightKey->getMatchColumn();
        $letSimple = empty($fields) ||
            (count($fields) === 1 && current($fields) === $rColumn);

        if (!in_array($rColumn, $fields)) {
            $fields[] = $rColumn;
        }

        $fields = array_map(fn($field) => "R.\"$field\"", $fields);
        $selectPhase = implode(',', $fields);

        $lTable = $this->leftKey->getTable();
        $lPrimary = $this->leftKey->getPrimaryIndex();
        $lColumn = $this->leftKey->getMatchColumn();

        $rTable = $this->rightKey->getTable();
        $rPrimary = $this->rightKey->getPrimaryIndex();

        $tName = $this->tableStructure->getTableName();
        $lForeignKey = $this->tableStructure->getLeftForeign();
        $rForeignKey = $this->tableStructure->getRightForeign();

        $sqlText = "
select $selectPhase
from $tName LR 
join $lTable L on LR.$lForeignKey = L.$lPrimary 
join $rTable R on LR.$rForeignKey = R.$rPrimary
where L.$lColumn=:l_value
ORDER BY 1";

        $query = $this->db->prepare($sqlText);

        $lValue = $linkage->getLeftValue();
        $query->bindParam(':l_value', $lValue);

        $result = $query->execute() !== false;
        $data = $result ? $query->fetchAll(PDO::FETCH_ASSOC) : [];

        if ($letSimple) {
            $data = array_column($data, $rColumn);
        }
        if (!$letSimple) {
            $data = (new ColumnManager($data))->indexWith($rColumn);
        }

        return $data;
    }
}
