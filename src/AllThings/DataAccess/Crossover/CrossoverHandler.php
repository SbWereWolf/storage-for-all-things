<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\DataAccess\Crossover;


use AllThings\DataAccess\Retrievable;
use PDO;

class CrossoverHandler implements ICrossoverHandler, Retrievable
{
    private $container;
    private $dataPath;
    private ICrossoverTable $tableStructure;
    private $leftKey;
    private IForeignKey $rightKey;

    public function __construct(
        ICrossover $container,
        IForeignKey $leftKey,
        IForeignKey $rightKey,
        ICrossoverTable $tableStructure,
        PDO $dataPath
    ) {
        $this->container = $container->getCrossoverCopy();
        $this->dataPath = $dataPath;
        $this->tableStructure = $tableStructure;
        $this->leftKey = $leftKey;
        $this->rightKey = $rightKey;
    }

    public function combine(): bool
    {
        $writer = $this->getCrossoverWriter();

        $result = $writer->insert($this->container);

        return $result;
    }

    private function getCrossoverWriter(): CrossoverWriter
    {
        $location = new CrossoverLocation(
            $this->leftKey,
            $this->rightKey,
            $this->tableStructure,
            $this->dataPath
        );

        return $location;
    }

    public function push(ICrossover $crossover): bool
    {
        $writer = $this->getCrossoverWriter();

        $result = $writer->update($crossover, $this->container);

        return $result;
    }

    public function pull(ICrossover $crossover): bool
    {
        $reader = $this->getCrossoverReader();

        $result = $reader->select($crossover);

        $isSuccess = $result === true;
        if ($isSuccess) {
            $this->container = $crossover->getCrossoverCopy();
        }

        return $result;
    }

    private function getCrossoverReader(): CrossoverReader
    {
        $source = new CrossoverSource($this->leftKey, $this->rightKey, $this->tableStructure, $this->dataPath);

        return $source;
    }

    public function retrieveData(): ICrossover
    {
        $data = $this->container->getCrossoverCopy();

        return $data;
    }

    public function has(): bool
    {
        return !is_null($this->container);
    }
}
