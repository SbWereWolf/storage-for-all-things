<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 3:25
 */

namespace AllThings\ControlPanel\Relation;

use AllThings\ControlPanel\ContentAccessFactory;
use AllThings\ControlPanel\Specification;
use AllThings\SearchEngine\Searchable;
use PDO;

class SpecificationFactory
{
    private PDO $db;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    public function make(string $code): Specification
    {
        $factory = new ContentAccessFactory(
            $this->db,
            Searchable::DATA_LOCATION
        );
        $product = new Specification($code, $factory,);

        return $product;
    }
}