<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\ControlPanel\Relation;

use AllThings\ControlPanel\ContentAccessFactory;
use AllThings\ControlPanel\Specification;
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