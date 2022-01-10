<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\DataAccess\Crossover;

use AllThings\DataAccess\Linkage\LinkageHandler;

class CrossoverHandler
    extends LinkageHandler
    implements ICrossoverHandler
{
    private ?ICrossover $container = null;

    private function getWriter(): CrossoverWriter
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $location = new CrossoverLocation(
            $this->leftKey,
            $this->rightKey,
            $this->tableStructure,
            $this->db
        );

        return $location;
    }

    public function setSubject(
        ICrossover $crossover
    ): ICrossoverHandler {
        $this->container = $crossover;

        return $this;
    }

    public function put(ICrossover $crossover): bool
    {
        $writer = $this->getWriter();

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $writer->update($this->container, $crossover);

        return $result;
    }
}
