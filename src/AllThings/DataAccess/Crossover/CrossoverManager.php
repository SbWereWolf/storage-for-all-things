<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 6:19
 */

namespace AllThings\DataAccess\Crossover;

use AllThings\DataAccess\Linkage\ILinkageTable;
use AllThings\DataAccess\Linkage\LinkageManager;
use PDO;

class CrossoverManager
    extends LinkageManager
    implements ICrossoverManager
{
    private ICrossoverHandler $crossoverHandler;

    /**
     * @param PDO           $db
     * @param ILinkageTable $table
     */
    public function __construct(
        PDO $db,
        ILinkageTable $table
    ) {
        parent::__construct($db, $table);

        $this->crossoverHandler = new CrossoverHandler(
            $db,
            $table,
        );
    }

    public function setSubject(
        ICrossover $crossover
    ): ICrossoverManager {
        $this->crossoverHandler->setSubject($crossover);

        return $this;
    }

    public function store(ICrossover $crossover): bool
    {
        $result = $this->crossoverHandler->put($crossover);

        return $result;
    }
}
