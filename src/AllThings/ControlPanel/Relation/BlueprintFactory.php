<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 6:19
 */

namespace AllThings\ControlPanel\Relation;

use AllThings\ControlPanel\EssenceRelated;
use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\LinkageManager;
use AllThings\DataAccess\Linkage\LinkageTable;
use PDO;

class BlueprintFactory
{
    private PDO $db;

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

        $category = new EssenceRelated($essence, $details,);

        return $category;
    }
}