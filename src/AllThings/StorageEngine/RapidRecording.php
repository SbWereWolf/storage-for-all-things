<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 4/10/22, 2:45 PM
 */

namespace AllThings\StorageEngine;

use AllThings\Blueprint\Relation\BlueprintFactory;
use AllThings\Blueprint\Relation\SpecificationFactory;
use AllThings\DataAccess\Crossover\ICrossover;
use AllThings\SearchEngine\Converter;
use AllThings\SearchEngine\Searchable;
use Exception;

class RapidRecording extends DBObject implements Installation
{
    public const STRUCTURE_PREFIX = 'auto_t_';
    public const SEPARATORS = ['.', ':', '-', '+', '@', '#', '&',];

    public function drop(): bool
    {
        $linkToData = $this->getDb();

        $ddl = "DROP TABLE IF EXISTS {$this->name()}";
        $affected = $linkToData->exec($ddl);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $isSuccess = $affected !== false;

        return $isSuccess;
    }

    /**
     * @param string $dml
     *
     * @return bool
     */
    private function executeSql(string $dml): bool
    {
        $affected = $this->db->exec($dml);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $affected !== false;

        return $result;
    }

    /**
     * @throws Exception
     */
    public function setup(
        string $attribute = '',
        string $dataType = ''
    ): bool {
        $result = false;
        if (!$attribute) {
            $result = $this->setupTable();
        }
        if ($attribute && !$dataType) {
            $blueprint = (new BlueprintFactory($this->db))
                ->make($this->getEssence());
            $attributes = $blueprint->list([Searchable::DATA_TYPE_FIELD]);

            $dataType = $attributes[$attribute][Searchable::DATA_TYPE_FIELD];
        }
        if ($attribute && $dataType) {
            $result = $this->setupColumn($attribute, $dataType);
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function refresh(array $values = []): bool
    {
        $linkToData = $this->getDb();
        $essence = $this->getEssence();

        $attributes = [];
        if (!$values) {
            $blueprint = (new BlueprintFactory($this->db))
                ->make($essence);
            $attributes = $blueprint->list();
        }
        $columnNames = [];
        foreach ($attributes as $code) {
            $columnNames[$code] = "\"$code\"";
        }

        $tableNames = '';
        $prefix = '';
        $viewNames = '';
        $view = null;
        $result = false;
        if ($columnNames) {
            $prefix = 'v.';
            $viewNames = $prefix . implode(",$prefix", $columnNames);
            $tableNames = implode(',', $columnNames);

            $view = new DirectReading(
                $essence,
                $this->getDb()
            );
            /* Удаляем из таблицы записи, которых нет в представлении */
            /** @noinspection SqlInsertValues */
            $dml = "
DELETE FROM {$this->name()}
WHERE thing_id in (
    SELECT t.thing_id
    FROM {$view->name()} as v
    RIGHT JOIN {$this->name()} t
    on {$prefix}thing_id = t.thing_id
    WHERE {$prefix}thing_id IS NULL 
    ORDER BY t.thing_id
)
";
            $result = $this->executeSql($dml);
        }

        /* Добавляем в таблицу недостающие записи из представления */
        if ($result) {
            /** @noinspection SqlInsertValues */
            $dml = "
INSERT INTO {$this->name()}(thing_id,code,$tableNames)
SELECT {$prefix}thing_id,{$prefix}code,$viewNames
FROM {$view->name()} as v
LEFT JOIN {$this->name()} t
on v.thing_id = t.thing_id
WHERE t.thing_id IS NULL
";
            $result = $this->executeSql($dml);
        }

        if ($values) {
            $blueprint = (new BlueprintFactory($this->db))
                ->make($essence);
            $attributes = $blueprint->list(
                [Searchable::DATA_TYPE_FIELD]
            );
        }

        $value = '';
        $pieces = [];
        $setParts = [];
        foreach ($values as $index => $value) {
            /* @var ICrossover $value */
            $attribute = $value->getRightValue();
            $content = $value->getContent();

            $specification = (new SpecificationFactory($this->db))
                ->make($essence);
            $specification->define([$attribute => $content]);

            $type =
                $attributes[$attribute][Searchable::DATA_TYPE_FIELD];
            $format = Converter::getFieldFormat($type);

            $pieces[":new_value$index"] = $content;
            $setParts[] = "\"$attribute\"" .
                " = :new_value$index::$format";
        }
        $setClause = implode(',', $setParts);
        $query = false;
        if ($setClause) {
            $dml = "
UPDATE {$this->name()}
SET
    $setClause
WHERE thing_id = (
    SELECT id
    FROM thing
    WHERE code = :thing_code
)
";
            $query = $linkToData->prepare($dml);
            $thing = $value->getLeftValue();
            $query->bindParam(':thing_code', $thing);
        }
        foreach ($pieces as $placeholder => $content) {
            $query->bindParam($placeholder, $pieces[$placeholder]);
        }
        if ($query) {
            $result = $query->execute();
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    private function setupTable(): bool
    {
        $isSuccess = $this->drop();

        $linkToData = $this->getDb();
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
        $columnNames = [];
        $indexes = [];
        foreach ($attributes as $code => $settings) {
            $sqlType = Converter::getFieldFormat(
                $settings[Searchable::DATA_TYPE_FIELD]
            );
            $name = "\"$code\"";

            $columns[] = "$name $sqlType";
            $columnNames[] = $name;

            $stripped = str_replace(static::SEPARATORS, '', $code);
            $indexes[] = "CREATE INDEX {$this->name()}_{$stripped}_ix"
                . " on {$this->name()}($name);";
        }

        if ($isSuccess) {
            $columnsPhase = implode(',', $columns);
            $names = implode(',', $columnNames);
            $tablePrimaryKey = "{$essence}_pk";

            $ddl = "
CREATE TABLE {$this->name()}
(    
	thing_id integer constraint $tablePrimaryKey primary key,    
    code VARCHAR(255) NOT NULL,
    $columnsPhase
)
";
            $affected = $linkToData->exec($ddl);
            $isSuccess = $affected !== false;
        }

        if ($isSuccess) {
            $view = new DirectReading(
                $this->getEssence(),
                $this->getDb()
            );
            /** @noinspection SqlInsertValues */
            $dml = "
INSERT INTO {$this->name()}(thing_id,code,$names)
SELECT thing_id,code,$names
FROM {$view->name()}
";
            $affected = $linkToData->exec($dml);
            $isSuccess = $affected !== false;

            $ddl = implode('', $indexes);
            /** @noinspection PhpUnusedLocalVariableInspection */
            $affected = $linkToData->exec($ddl);
        }

        return $isSuccess;
    }

    /**
     * @param string $attribute
     * @param string $dataType
     *
     * @return bool
     * @throws Exception
     */
    private function setupColumn(string $attribute, string $dataType): bool
    {
        $sqlType = Converter::getFieldType($dataType);
        $name = "\"$attribute\"";

        $ddl = "
ALTER TABLE {$this->name()} ADD COLUMN $name $sqlType
";
        $linkToData = $this->getDb();

        $affected = $linkToData->exec($ddl);
        $result = $affected !== false;

        $stripped = str_replace(static::SEPARATORS, '', $attribute);
        $columnIndex = "CREATE INDEX {$this->name()}_{$stripped}_ix"
            . " on {$this->name()}($name);";
        /** @noinspection PhpUnusedLocalVariableInspection */
        $affected = $linkToData->exec($columnIndex);

        return $result;
    }

    public function prune(string $attribute): bool
    {
        $name = "\"$attribute\"";
        $ddl = "
ALTER TABLE {$this->name()} DROP COLUMN $name
";

        $affected = $this->getDb()->exec($ddl);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $affected !== false;

        return $result;
    }
}
