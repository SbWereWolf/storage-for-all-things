<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 17:50
 */

namespace AllThings\DataAccess\Linkage;

use AllThings\DataAccess\Extractable;
use AllThings\DataAccess\Haves;
use PDO;

class LinkageSource
    implements ColumnReader,
               Extractable,
               Haves
{
    protected $tableStructure;
    protected $db;
    protected $rightKey;
    protected $leftKey;
    private array $dataSet = [];

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

    public function getForeignColumn(ILinkage $linkage): bool
    {
        $lTable = $this->leftKey->getTable();
        $lColumn = $this->leftKey->getColumn();
        $lIndex = $this->leftKey->getIndex();

        $rTable = $this->rightKey->getTable();
        $rColumn = $this->rightKey->getColumn();
        $rIndex = $this->rightKey->getIndex();

        $tName = $this->tableStructure->getTableName();
        $lKey = $this->tableStructure->getLeftColumn();
        $rKey = $this->tableStructure->getRightColumn();

        $sqlText = "
select R.{$rIndex} as code 
from $tName LR 
join $lTable L on LR.{$lKey} = L.{$lColumn} 
join $rTable R on LR.{$rKey} = R.{$rColumn}
where L.{$lIndex}=:l_value";

        $connection = $this->db;
        $query = $connection->prepare($sqlText);

        $lValue = $linkage->getLeftValue();
        $query->bindParam(':l_value', $lValue);

        $result = $query->execute() !== false;

        $data = null;
        if ($result) {
            $data = $query->fetchAll();
        }

        $this->dataSet = [];
        if ($data) {
            $this->dataSet = array_column($data, 'code');
        }

        $result = $result && !empty($this->dataSet);

        return $result;
    }

    public function has(): bool
    {
        $has = !empty($this->dataSet);

        return $has;
    }

    public function extract(): array
    {
        $result = $this->dataSet;

        return $result;
    }
}
