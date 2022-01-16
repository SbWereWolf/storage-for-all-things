<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\StorageEngine;

use AllThings\ControlPanel\Relation\BlueprintFactory;
use AllThings\SearchEngine\Converter;
use AllThings\SearchEngine\Searchable;
use Exception;

class DirectReading extends DBObject implements Installation
{
    public const STRUCTURE_PREFIX = 'auto_v_';

    /**
     * @throws Exception
     */
    public function setup(string $attribute = '', string $dataType = ''): bool
    {
        $linkToData = $this->getDb();

        $ddl = "DROP VIEW IF EXISTS {$this->name()}";
        $affected = $linkToData->exec($ddl);
        $isSuccess = $affected !== false;

        $essence = $this->getEssence();
        $attributes = [];
        if ($isSuccess) {
            $blueprint = (new BlueprintFactory($linkToData))
                ->make($essence);
            $attributes = $blueprint->list(
                [Searchable::DATA_TYPE_FIELD]
            );
        }

        $columns = [];
        foreach ($attributes as $attribute => $settings) {
            $table = Converter::getDataLocation(
                $settings[Searchable::DATA_TYPE_FIELD]
            );

            $column = "
SELECT
    C.content
FROM
    attribute A
    JOIN $table C
    ON C.attribute_id = A.id
WHERE
        A.code = '$attribute'
    AND C.thing_id = ET.thing_id
";
            $columns[$attribute] = "($column) AS \"$attribute\"";
        }

        $selectPhase = implode(",", $columns);
        $contentRequest = "
SELECT
    T.id AS thing_id,
    T.code AS code,
    $selectPhase
FROM
    essence E
    JOIN  essence_thing ET
    ON E.id = ET.essence_id
    JOIN thing T
    ON ET.thing_id = T.id
WHERE 
    E.code = '$essence'
";
        $ddl = "CREATE VIEW {$this->name()} AS $contentRequest";
        $affected = $linkToData->exec($ddl);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $affected !== false;

        return $result;
    }

    public function refresh(array $values = []): bool
    {
        return true;
    }

    /**
     * @throws Exception
     */
    public function prune(string $attribute): bool
    {
        return $this->setup();
    }
}
