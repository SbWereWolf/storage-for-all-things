<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 13:52
 */

namespace AllThings\ControlPanel;

use AllThings\Blueprint\Attribute\Attribute;
use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\DataAccess\Crossover\CrossoverManager;
use AllThings\DataAccess\Crossover\ICrossoverManager;
use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\LinkageTable;
use Exception;
use PDO;

class ContentAccessFactory
{
    private PDO $db;
    private array $typeToLocation;

    /**
     * @param PDO $connection
     */
    public function __construct(
        PDO $connection,
        array $typeToLocation,
    ) {
        $this->db = $connection;
        $this->typeToLocation = $typeToLocation;
    }

    public function makeContentAccess(
        string $attribute
    ): ICrossoverManager {
        $default = Attribute::GetDefaultAttribute();
        $default->setCode($attribute);

        $manager = new AttributeManager(
            $attribute,
            'attribute',
            $this->db,
        );
        $manager->setAttribute($default);

        $manager->browse();
        $dataType = $manager->retrieve()->getDataType();

        $isAcceptable = in_array(
            $dataType,
            array_keys($this->typeToLocation),
            true
        );
        if (!$isAcceptable) {
            throw new Exception(
                'Data location'
                . " for `$dataType` is not defined"
            );
        }

        $table = $this->typeToLocation[$dataType];
        $contentManager = $this->makeAccess($table);

        return $contentManager;
    }

    private function makeAccess(string $table): ICrossoverManager
    {
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
        $contentTable = new LinkageTable(
            $table,
            'thing_id',
            'attribute_id',
        );
        $contentManager = new CrossoverManager(
            $this->db,
            $contentTable,
            $thingKey,
            $attributeKey,
        );

        return $contentManager;
    }

    public function makeAllAccess(array $attributes): array
    {
        $default = Attribute::GetDefaultAttribute();

        $types = [];
        foreach ($attributes as $attribute) {
            $default->setCode($attribute);

            $manager = new AttributeManager(
                $attribute,
                'attribute',
                $this->db,
            );
            $manager->setAttribute($default);

            $manager->browse();
            $dataType = $manager->retrieve()->getDataType();
            if (!in_array($dataType, $types)) {
                $types[] = $dataType;
            }
        }

        $accesses = [];
        foreach ($types as $type) {
            if (!in_array($type, array_keys($accesses))) {
                $table = $this->typeToLocation[$type];
                $accesses[$type] = $this->makeAccess($table);
            }
        }

        return $accesses;
    }
}