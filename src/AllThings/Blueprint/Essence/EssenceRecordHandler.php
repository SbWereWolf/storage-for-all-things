<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:45
 */

namespace AllThings\Blueprint\Essence;


use AllThings\DataAccess\Nameable\Hideable;
use AllThings\DataAccess\Nameable\Valuable;
use AllThings\DataAccess\Retrievable;
use PDO;

class EssenceRecordHandler implements Valuable, Hideable, Retrievable
{

    private $storageLocation = 'essence';
    private $dataSource = 'essence';
    private $dataPath;
    private $essence = null;

    public function __construct(IEssence $essence, PDO $dataPath)
    {
        $this->essence = $essence;
        $this->dataPath = $dataPath;
    }

    public function add(): bool
    {
        $essence = $this->essence->GetEssenceCopy();

        $result = ($this->getEssenceLocation())->insert($essence);

        $this->setEssence($result, $essence);

        return $result;
    }

    private function getEssenceLocation(): EssenceLocation
    {
        $storageLocation = new EssenceLocation($this->storageLocation, $this->dataPath);

        return $storageLocation;
    }

    /**
     * @param $result
     * @param $essence
     */
    private function setEssence(bool $result, IEssence $essence): void
    {
        if ($result) {
            $this->essence = $essence;
        }
        if (!$result) {
            $this->essence = null;
        }
    }

    public function hide(): bool
    {
        $essence = $this->essence->GetEssenceCopy();

        $result = ($this->getEssenceLocation())->setIsHidden($essence);

        $this->setEssence($result, $essence);

        return $result;
    }

    public function write(string $code): bool
    {
        $essence = $this->essence->GetEssenceCopy();

        $target = $essence;
        if ($code) {
            $target = $this->setEssenceByCode($code);
        }

        $result = ($this->getEssenceLocation())->update($target, $essence);

        $this->setEssence($result, $essence);

        return $result;
    }

    /**
     * @param string $code
     * @return IEssence
     */
    private function setEssenceByCode(string $code): IEssence
    {
        $essence = Essence::GetDefaultEssence();
        $essence->setCode($code);

        return $essence;
    }

    public function read(): bool
    {
        $essence = $this->essence->GetEssenceCopy();

        $result = ($this->getEssenceSource())->select($essence);

        $this->setEssence($result, $essence);

        return $result;
    }

    private function getEssenceSource(): EssenceSource
    {
        $repository = new EssenceSource($this->dataSource, $this->dataPath);
        return $repository;
    }

    public function retrieveData(): IEssence
    {
        $essence = $this->essence->GetEssenceCopy();

        return $essence;
    }

    public function has(): bool
    {
        return !is_null($this->essence);
    }
}
