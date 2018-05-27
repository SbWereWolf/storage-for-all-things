<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 3:50
 */

namespace AllThings\DataAccess\Implementation;


use AllThings\DataAccess\Core\PrimitiveWriter;
use AllThings\DataObject\IForeignKey;

class EssenceAttributeLocation implements PrimitiveWriter
{

    const ESSENCE_IDENTIFIER = 'essence';
    const ATTRIBUTE_IDENTIFIER = 'attribute';
    private $essenceKey = '';
    private $attributeKey = '';
    private $storageLocation;

    public function __construct(IForeignKey $essenceKey, IForeignKey $attributeKey, \PDO $storageLocation)
    {
        $this->essenceKey = $essenceKey;
        $this->attributeKey = $attributeKey;
        $this->storageLocation = $storageLocation;
    }

    function insert(array $linkage): bool
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

        $connection->beginTransaction();
        $query = $connection->prepare($sqlText);

        $essenceIdentifier = $linkage[self::ESSENCE_IDENTIFIER];
        $query->bindParam(':essence_code', $essenceIdentifier);

        $attributeIdentifier = $linkage[self::ATTRIBUTE_IDENTIFIER];
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

    function delete(array $linkage): bool
    {
        $essenceTable = $this->essenceKey->getTable();
        $essenceColumn = $this->essenceKey->getColumn();
        $essenceIndex = $this->essenceKey->getIndex();

        $attributeTable = $this->attributeKey->getTable();
        $attributeColumn = $this->attributeKey->getColumn();
        $attributeIndex = $this->attributeKey->getIndex();

        $sqlText = "delete from essence_attribute where " .
            " essence_id=(select $essenceColumn from $essenceTable where $essenceIndex=:essence_code)"
            . " AND attribute_id=(select $attributeColumn from $attributeTable where $attributeIndex=:attribute_code)";
        $connection = $this->storageLocation;

        $connection->beginTransaction();
        $query = $connection->prepare($sqlText);

        $essenceIdentifier = $linkage[self::ESSENCE_IDENTIFIER];
        $query->bindParam(':essence_code', $essenceIdentifier);

        $attributeIdentifier = $linkage[self::ATTRIBUTE_IDENTIFIER];
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
