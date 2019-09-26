<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 21:26
 */

namespace AllThings\DataAccess\Implementation;


use AllThings\DataAccess\Core\PrimitiveWriter;
use AllThings\DataObject\IForeignKey;
use PDO;

class EssenceThingLocation implements PrimitiveWriter
{

    const ESSENCE_IDENTIFIER = 'essence';
    const THING_IDENTIFIER = 'thing';
    private $essenceKey = '';
    private $thingKey = '';
    private $storageLocation;

    public function __construct(IForeignKey $essenceKey, IForeignKey $thingKey, PDO $storageLocation)
    {
        $this->essenceKey = $essenceKey;
        $this->thingKey = $thingKey;
        $this->storageLocation = $storageLocation;
    }

    function insert(array $linkage): bool
    {
        $essenceTable = $this->essenceKey->getTable();
        $essenceColumn = $this->essenceKey->getColumn();
        $essenceIndex = $this->essenceKey->getIndex();

        $thingTable = $this->thingKey->getTable();
        $thingColumn = $this->thingKey->getColumn();
        $thingIndex = $this->thingKey->getIndex();

        $sqlText = 'insert into essence_thing (essence_id,thing_id)' .
            "values((select $essenceColumn from $essenceTable where $essenceIndex=:essence_code),"
            . "(select $thingColumn from $thingTable where $thingIndex=:thing_code))";
        $connection = $this->storageLocation;
        $query = $connection->prepare($sqlText);

        $essenceIdentifier = $linkage[self::ESSENCE_IDENTIFIER];
        $query->bindParam(':essence_code', $essenceIdentifier);

        $thingIdentifier = $linkage[self::THING_IDENTIFIER];
        $query->bindParam(':thing_code', $thingIdentifier);

        $result = $query->execute();

        return $result;
    }

    function delete(array $linkage): bool
    {
        $essenceTable = $this->essenceKey->getTable();
        $essenceColumn = $this->essenceKey->getColumn();
        $essenceIndex = $this->essenceKey->getIndex();

        $thingTable = $this->thingKey->getTable();
        $thingColumn = $this->thingKey->getColumn();
        $thingIndex = $this->thingKey->getIndex();

        $sqlText = "delete from essence_thing where " .
            " essence_id=(select $essenceColumn from $essenceTable where $essenceIndex=:essence_code)"
            . " AND thing_id=(select $thingColumn from $thingTable where $thingIndex=:thing_code)";
        $connection = $this->storageLocation;
        $query = $connection->prepare($sqlText);

        $essenceIdentifier = $linkage[self::ESSENCE_IDENTIFIER];
        $query->bindParam(':essence_code', $essenceIdentifier);

        $thingIdentifier = $linkage[self::THING_IDENTIFIER];
        $query->bindParam(':thing_code', $thingIdentifier);

        $result = $query->execute();

        return $result;
    }
}
