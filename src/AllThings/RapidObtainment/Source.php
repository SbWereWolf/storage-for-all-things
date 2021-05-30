<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 01.12.19 0:42
 */

namespace AllThings\RapidObtainment;


use AllThings\Essence\EssenceAttributeManager;
use AllThings\StorageEngine\Installation;
use PDO;

class Source implements Installation
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
        $essence = $this->getEssence();
        $linkToData = $this->getLinkToData();

        $manager = new EssenceAttributeManager($essence, '', $linkToData);
        $isSuccess = $manager->getAssociated();
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
        $name = $this->name();
        $ddl = "
CREATE MATERIALIZED VIEW $name AS
$contentRequest
";
        $affected = $linkToData->exec($ddl);
        $result = $affected !== false;

        return $result;
    }

    /**
     * @return string
     */
    public function getEssence(): string
    {
        return $this->essence;
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

}
