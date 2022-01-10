<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
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
     * @param ILinkageTable $location
     * @param ForeignKey    $leftKey
     * @param ForeignKey    $rightKey
     */
    public function __construct(
        PDO $db,
        ILinkageTable $location,
        ForeignKey $leftKey,
        ForeignKey $rightKey
    ) {
        parent::__construct($db, $location, $leftKey, $rightKey);

        $this->crossoverHandler = new CrossoverHandler(
            $leftKey,
            $rightKey,
            $location,
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
