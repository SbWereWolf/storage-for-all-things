<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 29.05.2021, 4:53
 */

namespace AllThings\DirectReading;


use AllThings\Essence\EssenceAttributeManager;
use AllThings\StorageEngine\Installation;
use PDO;

class Source implements Installation
{
    const STRUCTURE_PREFIX = 'auto_v_';

    private $essence = '';
    /**
     * @var PDO
     */
    private $linkToData;

    public function __construct(string $essence, PDO $linkToData)
    {
        $this->setEssence($essence)->setLinkToData($linkToData);
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
    T.id AS id,
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
CREATE OR REPLACE VIEW $name AS
$contentRequest
";
        $affected = $linkToData->exec($ddl);
        $result = $affected !== false;

        return $result;
    }

    public function name(): string
    {
        $name = self::STRUCTURE_PREFIX . $this->getEssence();

        return $name;
    }

    /**
     * @param string $essence
     *
     * @return Source
     */
    private function setEssence(string $essence): Source
    {
        $this->essence = $essence;
        return $this;
    }

    /**
     * @return string
     */
    public function getEssence(): string
    {
        return $this->essence;
    }

    /**
     * @param PDO $linkToData
     *
     * @return Source
     */
    private function setLinkToData(PDO $linkToData): Source
    {
        $this->linkToData = $linkToData;
        return $this;
    }

    /**
     * @return PDO
     */
    public function getLinkToData(): PDO
    {
        return $this->linkToData;
    }

}
