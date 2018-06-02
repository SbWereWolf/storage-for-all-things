<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 02.06.18 21:34
 */

namespace AllThings\DataAccess\Handler;


use AllThings\DataAccess\Core\CrossoverReader;
use AllThings\DataAccess\Core\CrossoverWriter;
use AllThings\DataAccess\Implementation\CrossoverLocation;
use AllThings\DataAccess\Implementation\CrossoverSource;
use AllThings\DataObject\ICrossover;
use AllThings\DataObject\ICrossoverTable;
use AllThings\DataObject\IForeignKey;

class CrossoverHandler implements ICrossoverHandler, Retrievable
{
    private $container = null;
    private $dataPath = null;
    private $tableStructure = null;
    private $leftKey = null;
    private $rightKey = null;

    public function __construct(ICrossover $container, IForeignKey $leftKey, IForeignKey $rightKey, ICrossoverTable $tableStructure, \PDO $dataPath)
    {
        $this->container = $container->getCrossoverCopy();
        $this->dataPath = $dataPath;
        $this->tableStructure = $tableStructure;
        $this->leftKey = $leftKey;
        $this->rightKey = $rightKey;
    }

    function crossover(): \bool
    {
        $writer = $this->getCrossoverWriter();

        $result = $writer->insert($this->container);

        return $result;
    }

    private function getCrossoverWriter(): CrossoverWriter
    {
        $location = new CrossoverLocation($this->leftKey, $this->rightKey, $this->tableStructure, $this->dataPath);

        return $location;
    }

    function setCrossover(ICrossover $crossover): \bool
    {
        $writer = $this->getCrossoverWriter();

        $result = $writer->update($crossover, $this->container);

        return $result;
    }

    function getCrossover(ICrossover $crossover): \bool
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

    function retrieveData(): ICrossover
    {
        $data = $this->container->getCrossoverCopy();

        return $data;
    }
}
