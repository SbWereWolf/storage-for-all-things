<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 14:22
 */

namespace AllThings\DataAccess\Crossover;

use AllThings\DataAccess\Linkage\ForeignKey;
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
     * @param ForeignKey    $leftKey
     * @param ForeignKey    $rightKey
     */
    public function __construct(
        PDO $db,
        ILinkageTable $table,
        ForeignKey $leftKey,
        ForeignKey $rightKey
    ) {
        parent::__construct($db, $table, $leftKey, $rightKey);

        $this->crossoverHandler = new CrossoverHandler(
            $leftKey,
            $rightKey,
            $table,
            $db,
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
