<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 3:02
 */

namespace AllThings\ControlPanel\Relation;

use AllThings\ControlPanel\EssenceRelated;
use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\LinkageManager;
use AllThings\DataAccess\Linkage\LinkageTable;
use PDO;

class CategoryFactory
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
            'essence_attribute',
            'essence_id',
            'attribute_id',
        );
        $details = new LinkageManager(
            $this->db,
            $categoryTable,
            $essenceKey,
            $attributeKey,
        );

        $category = new EssenceRelated($essence, $details,);

        return $category;
    }
}