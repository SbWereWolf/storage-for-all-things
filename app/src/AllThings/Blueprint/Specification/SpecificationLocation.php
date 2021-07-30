<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */

namespace AllThings\Blueprint\Specification;


use AllThings\DataAccess\Crossover\ICrossover;
use AllThings\DataAccess\Crossover\IForeignKey;
use AllThings\DataAccess\Crossover\PrimitiveWriter;
use PDO;

class SpecificationLocation implements PrimitiveWriter
{
    private $essenceKey = '';
    private $attributeKey = '';
    private $storageLocation;

    public function __construct(IForeignKey $essenceKey, IForeignKey $attributeKey, PDO $storageLocation)
    {
        $this->essenceKey = $essenceKey;
        $this->attributeKey = $attributeKey;
        $this->storageLocation = $storageLocation;
    }

    public function insert(ICrossover $linkage): bool
    {
        $essenceTable = $this->essenceKey->getTable();
        $essenceColumn = $this->essenceKey->getColumn();
        $essenceIndex = $this->essenceKey->getIndex();

        $attributeTable = $this->attributeKey->getTable();
        $attributeColumn = $this->attributeKey->getColumn();
        $attributeIndex = $this->attributeKey->getIndex();

        $sqlText = 'insert into essence_attribute (essence_id,attribute_id)' .
            "values((select $essenceColumn from $essenceTable where $essenceIndex=:essence_code),"
            . "(select $attributeColumn from $attributeTable where $attributeIndex=:attribute_code))";
        $connection = $this->storageLocation;

        $query = $connection->prepare($sqlText);

        $essenceIdentifier = $linkage->getLeftValue();
        $query->bindParam(':essence_code', $essenceIdentifier);

        $attributeIdentifier = $linkage->getRightValue();
        $query->bindParam(':attribute_code', $attributeIdentifier);

        $result = $query->execute();

        return $result;
    }

    public function delete(ICrossover $linkage): bool
    {
        $essenceTable = $this->essenceKey->getTable();
        $essenceColumn = $this->essenceKey->getColumn();
        $essenceIndex = $this->essenceKey->getIndex();

        $attributeTable = $this->attributeKey->getTable();
        $attributeColumn = $this->attributeKey->getColumn();
        $attributeIndex = $this->attributeKey->getIndex();

        $sqlText = "delete from essence_attribute where "
            . " essence_id=(select $essenceColumn from $essenceTable where $essenceIndex=:essence_code)"
            . " AND attribute_id=(select $attributeColumn from $attributeTable where $attributeIndex=:attribute_code)";
        $connection = $this->storageLocation;

        $connection->beginTransaction();
        $query = $connection->prepare($sqlText);

        $essenceIdentifier = $linkage->getLeftValue();
        $query->bindParam(':essence_code', $essenceIdentifier);

        $attributeIdentifier = $linkage->getRightValue();
        $query->bindParam(':attribute_code', $attributeIdentifier);

        $result = $query->execute();

        $isSuccess = $result === true;
        if ($isSuccess) {
            $result = $connection->commit();
        }
        if (!$isSuccess) {
            $connection->rollBack();
        }

        return $result;
    }
}
