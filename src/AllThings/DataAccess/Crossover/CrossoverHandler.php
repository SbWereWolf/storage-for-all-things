<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 8:09
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
            $this->table,
            $this->db
        );

        return $location;
    }

    public function setSubject(
        ICrossover $crossover
    ): ICrossoverHandler {
        $copy = $crossover->getCrossoverCopy();
        $this->container = $copy;

        return $this;
    }

    public function put(ICrossover $crossover): bool
    {
        $writer = $this->getWriter();
        $copy = $this->container->getCrossoverCopy();

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $writer->update($crossover, $copy);
        if ($result) {
            $this->container = $copy->getCrossoverCopy();
        }

        return $result;
    }
}
