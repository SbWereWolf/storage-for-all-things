<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:46
 */

namespace AllThings\Blueprint\Specification;


use AllThings\DataAccess\Crossover\ColumnReader;
use AllThings\DataAccess\Crossover\ICrossover;
use AllThings\DataAccess\Crossover\IForeignKey;
use AllThings\DataAccess\Retrievable;
use PDO;

class SpecificationSource implements ColumnReader, Retrievable
{
    private $essenceKey = null;
    private $attributeKey = null;
    private $dataSource;
    private $dataSet = [];

    public function __construct(IForeignKey $essenceKey, IForeignKey $attributeKey, PDO $dataSource)
    {
        $this->essenceKey = $essenceKey;
        $this->attributeKey = $attributeKey;
        $this->dataSource = $dataSource;
    }

    public function select(ICrossover $linkage): bool
    {
        $essenceTable = $this->essenceKey->getTable();
        $essenceColumn = $this->essenceKey->getColumn();
        $essenceIndex = $this->essenceKey->getIndex();

        $attributeTable = $this->attributeKey->getTable();
        $attributeColumn = $this->attributeKey->getColumn();
        $attributeIndex = $this->attributeKey->getIndex();

        $sqlText = "
select A.$attributeIndex as code from essence_attribute EA 
join $essenceTable E on EA.essence_id = E.$essenceColumn 
join $attributeTable A on EA.attribute_id = A.$attributeColumn
where E.$essenceIndex=:essence_code
order by 1";

        $connection = $this->dataSource;
        $query = $connection->prepare($sqlText);

        $essenceIdentity = $linkage->getLeftValue();
        $query->bindParam(':essence_code', $essenceIdentity);

        $result = $query->execute();

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
            foreach ($data as $row) {
                $this->dataSet[] = $row['code'];
            }
        }

        return $result;
    }

    public function retrieveData(): array
    {
        $result = $this->dataSet;

        return $result;
    }

    public function has(): bool
    {
        return !is_null($this->dataSet);
    }
}
