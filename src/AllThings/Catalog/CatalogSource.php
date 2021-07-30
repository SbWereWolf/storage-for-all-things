<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:45
 */

namespace AllThings\Catalog;


use AllThings\DataAccess\Crossover\ColumnReader;
use AllThings\DataAccess\Crossover\ICrossover;
use AllThings\DataAccess\Crossover\IForeignKey;
use AllThings\DataAccess\Retrievable;
use PDO;

class CatalogSource implements ColumnReader, Retrievable
{
    public const ESSENCE_IDENTIFIER = 'essence';
    public const THING_IDENTIFIER = 'thing';
    private $essenceKey = null;
    private $thingKey = null;
    private $dataSource;
    private $dataSet = [];

    public function __construct(IForeignKey $essenceKey, IForeignKey $thingKey, PDO $dataSource)
    {
        $this->essenceKey = $essenceKey;
        $this->thingKey = $thingKey;
        $this->dataSource = $dataSource;
    }

    public function select(ICrossover $linkage): bool
    {
        $essenceTable = $this->essenceKey->getTable();
        $essenceColumn = $this->essenceKey->getColumn();
        $essenceIndex = $this->essenceKey->getIndex();

        $thingTable = $this->thingKey->getTable();
        $thingColumn = $this->thingKey->getColumn();
        $thingIndex = $this->thingKey->getIndex();

        $sqlText = "
select T.$thingIndex as code from essence_thing ET 
join $essenceTable E on ET.essence_id = E.$essenceColumn 
join $thingTable T on ET.thing_id = T.$thingColumn
where E.$essenceIndex=:essence_code";

        $connection = $this->dataSource;

        $connection->beginTransaction();
        $query = $connection->prepare($sqlText);

        $essenceIdentifier = $linkage[self::ESSENCE_IDENTIFIER];
        $query->bindParam(':essence_code', $essenceIdentifier);

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
        $this->dataSet = [];
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
        $has = !empty($this->retrieveData());

        return $has;
    }
}
