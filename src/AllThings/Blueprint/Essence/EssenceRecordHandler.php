<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 17:50
 */

namespace AllThings\Blueprint\Essence;

use AllThings\DataAccess\Haves;
use AllThings\DataAccess\Nameable\Valuable;
use AllThings\DataAccess\Retrievable;
use AllThings\DataAccess\Uniquable\UniqueHandler;
use PDO;

class EssenceRecordHandler
    extends UniqueHandler
    implements Valuable,
               Retrievable,
               Haves
{
    private string $dataSource;
    private ?IEssence $essence;

    public function __construct(
        string $uniqueness,
        string $locationName,
        PDO $pdo,
    ) {
        parent::__construct($uniqueness, $locationName, $pdo);

        $this->dataSource = $locationName;
    }

    private function getEssenceLocation(): EssenceLocation
    {
        $storageLocation = new EssenceLocation(
            $this->location,
            $this->dataPath,
        );

        return $storageLocation;
    }

    public function write(string $code): bool
    {
        $result = $this->getEssenceLocation()
            ->update($this->essence, $code);

        return $result;
    }

    public function read(): bool
    {
        $essence = $this->essence->GetEssenceCopy();

        $result = ($this->getEssenceSource())->select($essence);

        if ($result) {
            $this->essence = $essence;
        }

        return $result;
    }

    private function getEssenceSource(): EssenceSource
    {
        $repository = new EssenceSource(
            $this->dataSource,
            $this->dataPath,
        );
        return $repository;
    }

    public function retrieve(): IEssence
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
    public function setEssence(IEssence $essence): bool
    {
        $this->essence = $essence;

        return true;
    }
}
