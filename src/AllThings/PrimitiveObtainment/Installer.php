<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 01.10.2019, 0:41
 */

namespace AllThings\PrimitiveObtainment;


use AllThings\Essence\EssenceAttributeManager;
use AllThings\StorageEngine\Installation;
use PDO;

class Installer implements Installation
{
    /**
     * @var string
     */
    private $essence;
    /**
     * @var PDO
     */
    private $linkToData;

    public function __construct(string $essence, PDO $linkToData)
    {
        $this->essence = $essence;
        $this->linkToData = $linkToData;
    }

    public function setup(): bool
    {
        $essence = $this->essence;
        $linkToData = $this->linkToData;

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
        content C
    JOIN attribute A
    ON C.attribute_id = A.id
    WHERE
            C.thing_id = ET.thing_id
        AND A.code = '$attribute'
";
            $columns[$attribute] = "($column) AS \"$attribute\"";

        }

        $selectPhase = implode(",", $columns);
        $contentRequest = "
SELECT
    $selectPhase
FROM 
    essence_thing ET
WHERE
    ET.essence_id = ( SELECT id FROM essence WHERE code = '$essence' )
";
        $view = 'auto_v_' . $essence;
        $ddl = "
CREATE OR REPLACE VIEW $view AS
$contentRequest
";
        $affected = $linkToData->exec($ddl);
        $result = $affected !== false;

        return $result;
    }
}
