<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\StorageEngine;


use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\Blueprint\Specification\SpecificationManager;
use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\Linkage;
use AllThings\DataAccess\Linkage\LinkageManager;
use AllThings\DataAccess\Linkage\LinkageTable;
use PDO;

class DirectReading implements Installation
{
    public const STRUCTURE_PREFIX = 'auto_v_';

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
     * @return DirectReading
     */
    private function setLinkToData(PDO $linkToData): DirectReading
    {
        $this->linkToData = $linkToData;
        return $this;
    }

    /**
     * @param string $essence
     *
     * @return DirectReading
     */
    private function setEssence(string $essence): DirectReading
    {
        $this->essence = $essence;
        return $this;
    }

    public function setup(?IAttribute $attribute = null): bool
    {
        $linkToData = $this->getDb();

        $ddl = "DROP VIEW IF EXISTS {$this->name()}";
        $affected = $linkToData->exec($ddl);
        $isSuccess = $affected !== false;

        $essence = $this->getEssence();
        if ($isSuccess) {
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
                $this->linkToData,
                $specification,
                $essenceKey,
                $attributeKey,
            );

            $linkage = (new Linkage())->setLeftValue($essence);
            $isSuccess = $specificationManager->getAssociated($linkage);
        }
        if ($isSuccess) {
            $isSuccess = $specificationManager->has();
        }
        $attributes = [];
        if ($isSuccess) {
            $attributes = $specificationManager->retrieveData();
        }

        $columns = [];
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
        $result = $affected !== false;

        return $result;
    }

    /**
     * @return PDO
     */
    public function getDb(): PDO
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

    public function refresh(array $values = []): bool
    {
        return true;
    }

    public function prune(string $attribute): bool
    {
        return $this->setup();
    }
}
