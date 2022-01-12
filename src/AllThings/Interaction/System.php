<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 17:50
 */

namespace AllThings\Interaction;

use AllThings\ControlPanel\Category;
use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\LinkageManager;
use AllThings\DataAccess\Linkage\LinkageTable;
use PDO;

class System
{
    private PDO $db;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    /**
     * @return LinkageManager
     */
    public function getCategory(string $essence): Category
    {
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
        $specificationTable = new LinkageTable(
            'essence_attribute',
            'essence_id',
            'attribute_id',
        );
        $specification = new LinkageManager(
            $this->db,
            $specificationTable,
            $essenceKey,
            $attributeKey,
        );

        $category = new Category($essence, $specification,);

        return $category;
    }
}