<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 4/10/22, 2:45 PM
 */

namespace AllThings\Blueprint\Relation;

use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\LinkageManager;
use AllThings\DataAccess\Linkage\LinkageTable;
use PDO;

class BlueprintFactory
{
    private PDO $db;

    /**
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    public function make(
        string $essence
    ): EssenceRelated {
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
        $categoryTable = new LinkageTable(
            'essence_attribute', $essenceKey, $attributeKey,
        );
        $details = new LinkageManager(
            $this->db,
            $categoryTable,
        );

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $blueprint = new EssenceRelated($essence, $details,);

        return $blueprint;
    }
}