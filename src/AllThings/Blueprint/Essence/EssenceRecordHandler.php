<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\Blueprint\Essence;

use AllThings\DataAccess\Nameable\Valuable;
use AllThings\DataAccess\Retrievable;
use AllThings\DataAccess\Uniquable\UniqueHandler;
use Exception;

class EssenceRecordHandler extends UniqueHandler implements Valuable, Retrievable
{
    private string $dataSource = 'essence';
    private ?IEssence $essence;

    private function getEssenceLocation(): EssenceLocation
    {
        $storageLocation = new EssenceLocation($this->location, $this->dataPath);

        return $storageLocation;
    }

    /**
     * @param bool $result
     * @param IEssence $essence
     */
    private function assignEssence(bool $result, IEssence $essence): void
    {
        if ($result) {
            $this->essence = $essence;
        }
        if (!$result) {
            $this->essence = null;
        }
    }

    public function write(string $code): bool
    {
        $essence = $this->essence->GetEssenceCopy();

        $target = $essence;
        if ($code) {
            $target = $this->setEssenceByCode($code);
        }

        $result = ($this->getEssenceLocation())->update($target, $essence);

        $this->assignEssence($result, $essence);

        return $result;
    }

    /**
     * @param string $code
     * @return IEssence
     * @throws Exception
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

        $this->assignEssence($result, $essence);

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

    /**
     * @param IEssence $essence
     */
    public function setEssence(IEssence $essence): void
    {
        $this->essence = $essence;
    }
}
