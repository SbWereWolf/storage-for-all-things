<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 2022-04-10
 */

namespace AllThings\Blueprint\Relation;

use AllThings\ControlPanel\Product\Specification;
use AllThings\SearchEngine\Findable;
use JetBrains\PhpStorm\Pure;
use PDO;

class SpecificationFactory
{
    private PDO $db;

    /**
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    #[Pure]
    public function make(string $product): Specification
    {
        $factory = new ContentAccessFactory(
            $this->db,
            Findable::DATA_LOCATIONS
        );
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $specification = new Specification($product, $factory,);

        return $specification;
    }
}