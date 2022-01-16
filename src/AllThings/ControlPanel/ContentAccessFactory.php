<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\ControlPanel;

use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\DataAccess\Crossover\CrossoverManager;
use AllThings\DataAccess\Crossover\ICrossoverManager;
use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\LinkageTable;
use AllThings\SearchEngine\Converter;
use AllThings\SearchEngine\Searchable;
use Exception;
use PDO;

class ContentAccessFactory
{
    private PDO $db;
    private array $typeToLocation;

    /**
     * @param PDO $connection
     * @param array $typeToLocation
     */
    public function __construct(
        PDO $connection,
        array $typeToLocation,
    ) {
        $this->db = $connection;
        $this->typeToLocation = $typeToLocation;
    }

    /**
     * @throws Exception
     */
    public function makeContentAccess(
        string $attribute
    ): ICrossoverManager {
        $manager = new AttributeManager($this->db, 'attribute', '', 'code',);

        $attribs = $manager->properties(
            [$attribute],
            [Searchable::DATA_TYPE_FIELD]
        );
        $dataType = $attribs[$attribute][Searchable::DATA_TYPE_FIELD];

        $table = Converter::getDataLocation($dataType);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
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
            $table, $thingKey, $attributeKey,
        );
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $contentManager = new CrossoverManager(
            $this->db,
            $contentTable,
        );

        return $contentManager;
    }

    /**
     * @throws Exception
     */
    public function makeAllAccess(array $attributes): array
    {
        $manager = new AttributeManager(
            $this->db,
            'attribute',
        );

        $props = $manager->properties(
            $attributes,
            [Searchable::DATA_TYPE_FIELD]
        );
        $props = array_column($props, Searchable::DATA_TYPE_FIELD);

        $accesses = [];
        foreach ($props as $type) {
            if (!in_array($type, array_keys($accesses))) {
                $table = $this->typeToLocation[$type];
                $accesses[$type] = $this->makeAccess($table);
            }
        }

        return $accesses;
    }
}