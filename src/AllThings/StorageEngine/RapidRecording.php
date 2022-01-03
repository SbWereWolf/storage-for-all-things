<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 03.01.2022, 6:20
 */

namespace AllThings\StorageEngine;


use AllThings\Blueprint\Attribute\Attribute;
use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\Blueprint\Specification\SpecificationManager;
use AllThings\Content\ContentManager;
use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Crossover\CrossoverTable;
use AllThings\DataAccess\Crossover\ICrossover;
use AllThings\SearchEngine\Searchable;
use Exception;
use PDO;

class RapidRecording implements Installation
{
    public const STRUCTURE_PREFIX = 'auto_t_';
    public const SEPARATORS = ['.', ':', '-', '+', '@', '#', '&',];

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

    public function setup(?IAttribute $attribute = null): bool
    {
        $result = false;
        if (!$attribute) {
            $result = $this->setupTable();
        }
        if ($attribute) {
            $result = $this->setupColumn($attribute);
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

    public function refresh(array $values = []): bool
    {
        $linkToData = $this->getLinkToData();

        $isSuccess = false;
        if (!$values) {
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

        $pieces = [];
        $setParts = [];
        foreach ($values as $index => $value) {
            /* @var ICrossover $value */
            $attribute = $value->getRightValue();
            $table = SpecificationManager::getLocation(
                $attribute,
                $this->linkToData,
            );

            $contentTable = new CrossoverTable(
                $table,
                'thing_id',
                'attribute_id'
            );
            $handler = new ContentManager(
                $value,
                $linkToData,
                $contentTable
            );
            $handler->store($value);

            $format = SpecificationManager::getFormat(
                $attribute,
                $this->linkToData,
            );

            $pieces[":new_value$index"] = $value->getContent();
            $setParts[] = "\"{$value->getRightValue()}\"" .
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

    private function setupTable(): bool
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
        $indexes = [];
        foreach ($attributes as $attribute) {
            /* @var IAttribute $attribute */

            $dataType = $attribute->getDataType();
            switch ($dataType) {
                case Searchable::SYMBOLS:
                    $sqlType = 'VARCHAR(255)';
                    break;
                case Searchable::DECIMAL:
                    $sqlType = 'DECIMAL(14,4)';
                    break;
                case Searchable::TIMESTAMP:
                    $sqlType = 'TIMESTAMP WITH TIME ZONE';
                    break;
                case Searchable::INTERVAL:
                    $sqlType = 'INTERVAL';
                    break;
                default:

                    throw new Exception(
                        'SQL data type for'
                        . " `$dataType` is not defined"
                    );
            }

            $code = $attribute->getCode();
            $name = "\"{$code}\"";

            $columns[] = "$name $sqlType";
            $columnNames[] = $name;

            $stripped = str_replace(static::SEPARATORS, '', $code);
            $indexes[] = "CREATE INDEX {$this->name()}_{$stripped}_ix"
                . " on {$this->name()}($name);";
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
SELECT thing_id,code,$names
FROM {$view->name()}
";
            $affected = $linkToData->exec($dml);
            $result = $affected !== false;

            $ddl = implode('', $indexes);
            $affected = $linkToData->exec($ddl);
        }

        return $result;
    }

    /**
     * @param IAttribute $attribute
     * @return bool
     * @throws Exception
     */
    private function setupColumn(IAttribute $attribute): bool
    {
        $dataType = $attribute->getDataType();
        switch ($dataType) {
            case Searchable::SYMBOLS:
                $sqlType = 'VARCHAR(255)';
                break;
            case Searchable::DECIMAL:
                $sqlType = 'DECIMAL(14,4)';
                break;
            case Searchable::TIMESTAMP:
                $sqlType = 'TIMESTAMP WITH TIME ZONE';
                break;
            case Searchable::INTERVAL:
                $sqlType = 'INTERVAL';
                break;
            default:

                throw new Exception(
                    'SQL data type for'
                    . " `$dataType` is not defined"
                );
        }

        $code = $attribute->getCode();
        $name = "\"{$code}\"";

        $essence = $this->getEssence();

        $ddl = "
ALTER TABLE {$this->name()} ADD COLUMN {$name} $sqlType
";
        $linkToData = $this->getLinkToData();

        $affected = $linkToData->exec($ddl);
        $result = $affected !== false;

        $stripped = str_replace(static::SEPARATORS, '', $code);
        $columnIndex = "CREATE INDEX {$this->name()}_{$stripped}_ix"
            . " on {$this->name()}($name);";
        $affected = $linkToData->exec($columnIndex);

        return $result;
    }
}
