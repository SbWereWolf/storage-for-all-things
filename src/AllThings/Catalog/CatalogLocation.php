<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */

namespace AllThings\Catalog;


use AllThings\DataAccess\Crossover\ICrossover;
use AllThings\DataAccess\Crossover\IForeignKey;
use AllThings\DataAccess\Crossover\PrimitiveWriter;
use PDO;

class CatalogLocation implements PrimitiveWriter
{

    public const ESSENCE_IDENTIFIER = 'essence';
    public const THING_IDENTIFIER = 'thing';
    private $essenceKey = '';
    private $thingKey = '';
    private $storageLocation;

    public function __construct(IForeignKey $essenceKey, IForeignKey $thingKey, PDO $storageLocation)
    {
        $this->essenceKey = $essenceKey;
        $this->thingKey = $thingKey;
        $this->storageLocation = $storageLocation;
    }

    public function insert(ICrossover $linkage): bool
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

        $essenceIdentifier = $linkage->getLeftValue();
        $query->bindParam(':essence_code', $essenceIdentifier);

        $thingIdentifier = $linkage->getRightValue();
        $query->bindParam(':thing_code', $thingIdentifier);

        $result = $query->execute();

        return $result;
    }

    public function delete(ICrossover $linkage): bool
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
