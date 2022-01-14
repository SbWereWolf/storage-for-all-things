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

class CatalogFactory
{
    private PDO $db;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    public function make(string $essence): EssenceRelated
    {
        $essenceKey = new ForeignKey('essence', 'id', 'code');
        $thingKey = new ForeignKey('thing', 'id', 'code');
        $table = new LinkageTable(
            'essence_thing', $essenceKey, $thingKey,
        );
        $details = new LinkageManager(
            $this->db,
            $table,
        );

        $catalog = new EssenceRelated($essence, $details,);

        return $catalog;
    }
}