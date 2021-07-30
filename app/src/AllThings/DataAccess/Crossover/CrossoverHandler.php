<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */

namespace AllThings\DataAccess\Crossover;


use AllThings\DataAccess\Retrievable;
use PDO;

class CrossoverHandler implements ICrossoverHandler, Retrievable
{
    private ICrossover $container;
    private PDO $dataPath;
    private ICrossoverTable $tableStructure;
    private IForeignKey $leftKey;
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

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $writer->insert($this->container);

        return $result;
    }

    private function getCrossoverWriter(): CrossoverWriter
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
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

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $writer->update($this->container, $crossover);

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
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $source = new CrossoverSource($this->leftKey, $this->rightKey, $this->tableStructure, $this->dataPath);

        return $source;
    }

    public function retrieveData(): ICrossover
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $data = $this->container->getCrossoverCopy();

        return $data;
    }

    public function has(): bool
    {
        return !is_null($this->container);
    }
}
