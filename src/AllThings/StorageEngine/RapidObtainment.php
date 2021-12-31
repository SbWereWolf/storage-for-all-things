<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 31.12.2021, 13:37
 */

namespace AllThings\StorageEngine;


use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\Blueprint\Specification\SpecificationManager;
use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Crossover\ICrossover;
use PDO;

class RapidObtainment implements Installation
{
    public const STRUCTURE_PREFIX = 'auto_mv_';
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

    public function setup(?IAttribute $attribute = null): bool
    {
        $linkToData = $this->getLinkToData();

        $name = $this->name();
        $ddl = "DROP MATERIALIZED VIEW IF EXISTS $name";
        $affected = $linkToData->exec($ddl);
        $isSuccess = $affected !== false;

        if ($isSuccess) {
            $essence = $this->getEssence();
            $manager = new SpecificationManager($linkToData);
            $linkage = (new Crossover())->setLeftValue($essence);
            $isSuccess = $manager->getAssociated($linkage);
        }
        if ($isSuccess) {
            $isSuccess = $manager->has();
        }
        $attributes = [];
        if ($isSuccess) {
            $attributes = $manager->retrieveData();
        }

        $columns = [];
        $indexes = [];
        foreach ($attributes as $attribute) {
            $table = SpecificationManager::getLocation(
                $attribute,
                $this->linkToData
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
            $stripped = str_replace(static::SEPARATORS, '', $attribute);
            $indexes[] = 'DROP INDEX IF EXISTS'
                . " {$this->name()}_{$stripped}_ix;";
            $indexes[] = "CREATE INDEX {$this->name()}_{$stripped}_ix"
                . " on {$this->name()} (\"{$attribute}\");";
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

        if ($isSuccess) {
            $ddl = "CREATE MATERIALIZED VIEW $name AS $contentRequest";
            $affected = $linkToData->exec($ddl);
            $result = $affected !== false;

            $ddl = implode('', $indexes);
            $affected = $linkToData->exec($ddl);
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
        $name = self::STRUCTURE_PREFIX . $this->getEssence();

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
        $ddl = "
REFRESH MATERIALIZED VIEW {$this->name()}
";
        $linkToData = $this->getLinkToData();
        $affected = $linkToData->exec($ddl);
        $result = $affected !== false;

        return $result;
    }
}
