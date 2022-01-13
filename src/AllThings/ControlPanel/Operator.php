<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 13:52
 */

namespace AllThings\ControlPanel;

use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\LinkageManager;
use AllThings\DataAccess\Linkage\LinkageTable;
use AllThings\DataAccess\Nameable\Nameable;
use AllThings\DataAccess\Nameable\NamedEntity;
use AllThings\DataAccess\Nameable\NamedManager;
use AllThings\SearchEngine\Searchable;
use Exception;
use PDO;

class Operator
{
    private PDO $db;


    private const DATA_LOCATION = [
        Searchable::SYMBOLS => 'word',
        Searchable::DECIMAL => 'number',
        Searchable::TIMESTAMP => 'time_moment',
        Searchable::INTERVAL => 'time_interval',
    ];

    /**
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    public function makeCatalog(string $essence): EssenceRelated
    {
        $essenceKey = new ForeignKey('essence', 'id', 'code');
        $thingKey = new ForeignKey('thing', 'id', 'code');
        $table = new LinkageTable(
            'essence_thing',
            'essence_id',
            'thing_id',
        );
        $details = new LinkageManager(
            $this->db,
            $table,
            $essenceKey,
            $thingKey,
        );

        $catalog = new EssenceRelated($essence, $details,);

        return $catalog;
    }

    public function makeProduct(string $code): Product
    {
        $factory = new ContentAccessFactory(
            $this->db,
            static::DATA_LOCATION
        );
        $product = new Product($code, $factory,);

        return $product;
    }

    /**
     * @param string $code
     * @param string $title
     * @param string $description
     *
     * @return Nameable
     * @throws Exception
     */
    public function create(
        string $code,
        string $title = '',
        string $description = '',
    ): Nameable {
        $nameable = (new NamedEntity())->setCode($code);
        $thingManager = new NamedManager(
            $code,
            'thing',
            $this->db
        );

        $isSuccess = $thingManager->create();
        if (!$isSuccess) {
            throw new Exception(
                'Product must be created with success'
            );
        }

        if ($title) {
            $nameable->setTitle($title);
        }
        if ($description) {
            $nameable->setRemark($description);
        }
        $thingManager->setNamed($nameable);
        if ($title || $description) {
            $isSuccess = $thingManager->correct();
        }
        if (!$isSuccess) {
            throw new Exception(
                'Product must be updated with success'
            );
        }

        return $nameable;
    }
}