<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 9:02
 */

namespace AllThings\Blueprint\Essence;

use AllThings\DataAccess\DataTransfer\Haves;
use AllThings\DataAccess\DataTransfer\Retrievable;
use AllThings\DataAccess\Nameable\Valuable;
use AllThings\DataAccess\Uniquable\UniqueHandler;
use PDO;

class EssenceHandler
    extends UniqueHandler
    implements Valuable,
               Retrievable,
               Haves
{
    private string $dataSource;
    private ?IEssence $stuff;

    public function __construct(
        string $uniqueness,
        string $locationName,
        PDO $db,
    ) {
        parent::__construct($uniqueness, $locationName, $db);

        $this->dataSource = $locationName;
    }

    /**
     * @param IEssence $essence
     */
    public function setEssence(IEssence $essence): static
    {
        $this->stuff = $essence;

        return $this;
    }

    public function read(): bool
    {
        $source = $this->getSource();

        $result = $source->select($this->stuff);
        if (!$result) {
            $this->stuff = null;
        }

        return $result;
    }

    public function write(string $code): bool
    {
        $location = $this->getLocation();

        $result = $location->update($this->stuff, $code);
        if (!$result) {
            $this->stuff = null;
        }

        return $result;
    }

    public function retrieve(): IEssence
    {
        $essence = $this->stuff->GetEssenceCopy();

        return $essence;
    }

    public function has(): bool
    {
        return !is_null($this->stuff);
    }

    private function getSource(): EssenceSource
    {
        $repository = new EssenceSource(
            $this->dataSource,
            $this->db,
        );
        return $repository;
    }

    private function getLocation(): EssenceLocation
    {
        $storageLocation = new EssenceLocation(
            $this->storageLocation,
            $this->db,
        );

        return $storageLocation;
    }
}
