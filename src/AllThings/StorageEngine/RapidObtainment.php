<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */

namespace AllThings\StorageEngine;


use AllThings\Blueprint\Specification\SpecificationManager;
use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Crossover\ICrossover;
use PDO;

class RapidObtainment implements Installation
{
    public const STRUCTURE_PREFIX = 'auto_mv_';

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

    public function setup(): bool
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
        foreach ($attributes as $attribute) {
            $column = "
SELECT
    C.content
FROM
    attribute A
    JOIN content C
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
