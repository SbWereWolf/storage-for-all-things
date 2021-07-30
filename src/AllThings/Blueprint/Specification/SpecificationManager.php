<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */


namespace AllThings\Blueprint\Specification;


use AllThings\DataAccess\Crossover\ForeignKey;
use AllThings\DataAccess\Crossover\ICrossover;
use AllThings\DataAccess\Crossover\LinkageManager;
use AllThings\DataAccess\Retrievable;
use PDO;

class SpecificationManager implements LinkageManager, Retrievable
{
    private $dataSet = [];
    private SpecificationHandler $handler;

    public function __construct(PDO $dataPath)
    {
        $essenceForeignKey = new ForeignKey('essence', 'id', 'code');
        $attributeForeignKey = new ForeignKey('attribute', 'id', 'code');
        $this->handler = new SpecificationHandler(
            $dataPath,
            $essenceForeignKey,
            $attributeForeignKey
        );
    }

    public function linkUp(ICrossover $linkage): bool
    {
        $handler = $this->getHandler();

        $result = $handler->add($linkage);

        return $result;
    }

    /**
     * @return SpecificationHandler
     */
    private function getHandler(): SpecificationHandler
    {
        return $this->handler;
    }

    public function breakDown(ICrossover $linkage): bool
    {
        $handler = $this->getHandler();
        $result = $handler->remove($linkage);

        return $result;
    }

    public function getAssociated(ICrossover $linkage): bool
    {
        $handler = $this->getHandler();
        $result = $handler->getRelated($linkage);

        $isSuccess = $result === true;
        if ($isSuccess) {
            $this->dataSet = $handler->retrieveData();
        }

        return $result;
    }

    public function retrieveData()
    {
        $result = $this->dataSet;

        return $result;
    }

    public function has(): bool
    {
        return !is_null($this->dataSet);
    }
}
