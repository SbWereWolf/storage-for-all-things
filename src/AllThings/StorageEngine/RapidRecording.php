<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:46
 */

namespace AllThings\StorageEngine;


use AllThings\Blueprint\Attribute\Attribute;
use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\Blueprint\Specification\SpecificationManager;
use AllThings\Content\ContentManager;
use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Crossover\ICrossover;
use PDO;

class RapidRecording implements Installation
{
    public const STRUCTURE_PREFIX = 'auto_t_';

    private $essence = '';
    /**
     * @var PDO
     */
    private $linkToData;

    public function __construct(string $essence, PDO $linkToData)
    {
        $this->setEssence($essence)->setLinkToData($linkToData);
    }

    /**
     * @param PDO $linkToData
     *
     * @return self
     */
    private function setLinkToData(PDO $linkToData): self
    {
        $this->linkToData = $linkToData;
        return $this;
    }

    /**
     * @param string $essence
     *
     * @return self
     */
    private function setEssence(string $essence): self
    {
        $this->essence = $essence;
        return $this;
    }

    /**
     * @param string $dml
     * @return bool
     */
    private function executeSql(string $dml): bool
    {
        $affected = $this->linkToData->exec($dml);
        $result = $affected !== false;

        return $result;
    }

    public function setup(): bool
    {
        $linkToData = $this->getLinkToData();

        $ddl = "DROP TABLE IF EXISTS {$this->name()}";
        $affected = $linkToData->exec($ddl);
        $result = $affected !== false;

        $essence = $this->getEssence();
        $isSuccess = false;
        if ($result) {
            $essenceManager = new SpecificationManager(
                $linkToData
            );
            $linkage = (new Crossover())->setLeftValue($essence);
            $isSuccess = $essenceManager->getAssociated($linkage);
        }

        if ($isSuccess) {
            $isSuccess = $essenceManager->has();
        }
        $attributeCodes = [];
        if ($isSuccess) {
            $attributeCodes = $essenceManager->retrieveData();
        }

        $attributes = [];
        foreach ($attributeCodes as $code) {
            $subject = Attribute::GetDefaultAttribute();
            $subject->setCode($code);
            $attributeManager = new AttributeManager(
                $subject,
                $linkToData
            );

            $isSuccess = $attributeManager->browse();
            if ($isSuccess) {
                $isSuccess = $attributeManager->has();
            }
            if ($isSuccess) {
                $attributes[] = $attributeManager->retrieveData();
            }
        }
        $columns = [];
        $columnNames = [];
        foreach ($attributes as $attribute) {
            /* @var IAttribute $attribute */

            $datatype = 'VARCHAR(255)';
            /*
                        switch ($attribute->getDataType()) {
                            case Searchable::DECIMAL:
                                $datatype = 'NUMERIC(8,8)';
                                break;
                            case Searchable::TIMESTAMP:
                                $datatype = 'TIMESTAMP';
                                break;
                        }
            */

            $code = $attribute->getCode();
            $columns[] = "\"{$code}\" $datatype";
            $columnNames[] = "\"{$code}\"";
        }

        $columnsPhase = implode(',', $columns);
        $names = implode(',', $columnNames);
        $tablePrimaryKey = "{$essence}_pk";

        $ddl = "
CREATE TABLE {$this->name()}
(
    thing_id integer REFERENCES thing (id), 
    constraint $tablePrimaryKey primary key (thing_id),
    code VARCHAR(255) NOT NULL,
    $columnsPhase
)
";
        $affected = $linkToData->exec($ddl);
        $result = $affected !== false;

        if ($result) {
            $view = new DirectReading(
                $this->getEssence(),
                $this->getLinkToData()
            );
            /** @noinspection SqlInsertValues */
            $dml = "
INSERT INTO {$this->name()}(thing_id,code,$names)
SELECT id,code,$names
FROM {$view->name()}
";
            $affected = $linkToData->exec($dml);
            $result = $affected !== false;
        }

        return $result;
    }

    /**
     * @return PDO
     */
    public function getLinkToData(): PDO
    {
        return $this->linkToData;
    }

    public function name(): string
    {
        $name = static::STRUCTURE_PREFIX . $this->getEssence();

        return $name;
    }

    /**
     * @return string
     */
    public function getEssence(): string
    {
        return $this->essence;
    }

    public function refresh(?ICrossover $value = null): bool
    {
        $linkToData = $this->getLinkToData();

        $isSuccess = false;
        if (!$value) {
            $essenceManager = new SpecificationManager(
                $linkToData
            );
            $linkage = (new Crossover())
                ->setLeftValue($this->getEssence());
            $isSuccess = $essenceManager->getAssociated($linkage);
        }

        if ($isSuccess) {
            $isSuccess = $essenceManager->has();
        }
        $attributes = [];
        if ($isSuccess) {
            $attributes = $essenceManager->retrieveData();
        }
        $columnNames = [];
        foreach ($attributes as $code) {
            $columnNames[] = "\"{$code}\"";
        }

        $result = false;
        if ($columnNames) {
            $prefix = 'v.';
            $viewNames = $prefix . implode(",$prefix", $columnNames);
            $tableNames = implode(',', $columnNames);

            $view = new DirectReading(
                $this->getEssence(),
                $this->getLinkToData()
            );
            /* Удаляем из таблицы записи, которых нет в представлении */
            /** @noinspection SqlInsertValues */
            $dml = "
DELETE FROM {$this->name()}
WHERE thing_id in (
    SELECT t.thing_id
    FROM {$view->name()} as v
    RIGHT JOIN {$this->name()} t
    on {$prefix}id = t.thing_id
    WHERE {$prefix}id IS NULL 
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
SELECT {$prefix}id,{$prefix}code,$viewNames
FROM {$view->name()} as v
LEFT JOIN {$this->name()} t
on v.id = t.thing_id
WHERE t.thing_id IS NULL
";
            $result = $this->executeSql($dml);
        }

        if ($value) {
            $handler = new ContentManager($value, $linkToData);
            $handler->store($value);

            $dml = "
UPDATE {$this->name()}
SET
    \"{$value->getRightValue()}\" = :new_value
WHERE thing_id = (
    SELECT id
    FROM thing
    WHERE code = '{$value->getLeftValue()}'
)
";
            $query = $linkToData->prepare($dml);
            $content = $value->getContent();
            $query->bindParam(':new_value', $content);

            $result = $query->execute();
        }

        return $result;
    }
}
