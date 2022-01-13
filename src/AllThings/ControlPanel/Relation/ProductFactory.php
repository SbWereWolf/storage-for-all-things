<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 3:02
 */

namespace AllThings\ControlPanel\Relation;

use AllThings\ControlPanel\ContentAccessFactory;
use AllThings\ControlPanel\Product;
use AllThings\SearchEngine\Searchable;
use PDO;

class ProductFactory
{
    private PDO $db;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    public function make(string $code): Product
    {
        $factory = new ContentAccessFactory(
            $this->db,
            Searchable::DATA_LOCATION
        );
        $product = new Product($code, $factory,);

        return $product;
    }
}