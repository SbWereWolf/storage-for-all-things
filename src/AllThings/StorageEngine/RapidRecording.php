<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 17:50
 */

namespace AllThings\StorageEngine;

use AllThings\Blueprint\Attribute\Attribute;
use AllThings\Blueprint\Attribute\AttributeHelper;
use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\DataAccess\Crossover\CrossoverManager;
use AllThings\DataAccess\Crossover\ICrossover;
use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\Linkage;
use AllThings\DataAccess\Linkage\LinkageManager;
use AllThings\DataAccess\Linkage\LinkageTable;
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
    private $db;

    public function __construct(string $essence, PDO $linkToData)
    {
        $this->setEssence($essence)->setDb($linkToData);
    }

    /**
     * @param PDO $linkToData
     *
     * @return self
     */
    private function setDb(PDO $linkToData): static
    {
        $this->db = $linkToData;
        return $this;
    }

    /**
     * @param string $essence
     *
     * @return self
     */
    private function setEssence(string $essence): static
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
        $affected = $this->db->exec($dml);
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
    public function getDb(): PDO
    {
        return $this->db;
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
        $linkToData = $this->getDb();

        $attributes = [];
        if (!$values) {
            $essenceKey = new ForeignKey(
                'essence',
                'id',
                'code'
            );
            $attributeKey = new ForeignKey(
                'attribute',
                'id',
                'code'
            );
            $specification = new LinkageTable(
                'essence_attribute',
                'essence_id',
                'attribute_id',
            );
            $essenceManager = new LinkageManager(
                $this->db,
                $specification,
                $essenceKey,
                $attributeKey,
            );

            $linkage = (new Linkage())
                ->setLeftValue($this->getEssence());

            $attributes = $essenceManager->getAssociated($linkage);
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

        $pieces = [];
        $setParts = [];
        foreach ($values as $index => $value) {
            /* @var ICrossover $value */
            $attribute = $value->getRightValue();

            $table = AttributeHelper::getLocation(
                $attribute,
                $this->db,
            );
            $contentTable = new LinkageTable(
                $table,
                'thing_id',
                'attribute_id',
            );

            $thingKey = new ForeignKey(
                'thing',
                'id',
                'code'
            );
            $attributeKey = new ForeignKey(
                'attribute',
                'id',
                'code'
            );

            $manager = new CrossoverManager(
                $this->db,
                $contentTable,
                $thingKey,
                $attributeKey,
            );

            $manager->setSubject($value);
            $manager->store($value);

            $format = AttributeHelper::getFormat(
                $attribute,
                $this->db,
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
        $linkToData = $this->getDb();

        $ddl = "DROP TABLE IF EXISTS {$this->name()}";
        $affected = $linkToData->exec($ddl);
        $result = $affected !== false;

        $essence = $this->getEssence();
        $attributeCodes = [];
        if ($result) {
            $essenceKey = new ForeignKey(
                'essence',
                'id',
                'code'
            );
            $attributeKey = new ForeignKey(
                'attribute',
                'id',
                'code'
            );
            $specification = new LinkageTable(
                'essence_attribute',
                'essence_id',
                'attribute_id',
            );
            $specificationManager = new LinkageManager(
                $this->db,
                $specification,
                $essenceKey,
                $attributeKey,
            );

            $linkage = (new Linkage())->setLeftValue($essence);
            $attributeCodes = $specificationManager
                ->getAssociated($linkage);
        }

        $attributes = [];
        foreach ($attributeCodes as $code) {
            $subject = Attribute::GetDefaultAttribute();
            $subject->setCode($code);
            $attributeManager = new AttributeManager(
                $code,
                'attribute',
                $linkToData
            );
            $attributeManager->setSubject($subject);

            $isSuccess = $attributeManager->browse();
            if ($isSuccess) {
                $isSuccess = $attributeManager->has();
            }
            if ($isSuccess) {
                $attributes[] = $attributeManager->retrieve();
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
	thing_id integer constraint $tablePrimaryKey primary key,    
    code VARCHAR(255) NOT NULL,
    $columnsPhase
)
";
        $affected = $linkToData->exec($ddl);
        $result = $affected !== false;

        if ($result) {
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

        $ddl = "
ALTER TABLE {$this->name()} ADD COLUMN {$name} $sqlType
";
        $linkToData = $this->getDb();

        $affected = $linkToData->exec($ddl);
        $result = $affected !== false;

        $stripped = str_replace(static::SEPARATORS, '', $code);
        $columnIndex = "CREATE INDEX {$this->name()}_{$stripped}_ix"
            . " on {$this->name()}($name);";
        $affected = $linkToData->exec($columnIndex);

        return $result;
    }

    public function prune(string $attribute): bool
    {
        $name = "\"{$attribute}\"";
        $ddl = "
ALTER TABLE {$this->name()} DROP COLUMN {$name}
";

        $affected = $this->getDb()->exec($ddl);
        $result = $affected !== false;

        return $result;
    }
}
