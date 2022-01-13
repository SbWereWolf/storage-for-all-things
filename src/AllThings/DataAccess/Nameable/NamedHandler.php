<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 9:02
 */

namespace AllThings\DataAccess\Nameable;

use AllThings\DataAccess\DataTransfer\Haves;
use AllThings\DataAccess\DataTransfer\Retrievable;
use AllThings\DataAccess\Uniquable\UniqueHandler;
use PDO;

class NamedHandler
    extends UniqueHandler
    implements Valuable,
               Retrievable,
               Haves
{
    private string $dataSource;
    private ?Nameable $stuff;

    public function __construct(
        string $uniqueness,
        string $locationName,
        PDO $db,
    ) {
        parent::__construct($uniqueness, $locationName, $db);
        $this->dataSource = $locationName;
    }

    /**
     * @param Nameable $named
     */
    public function setNamed(Nameable $named): static
    {
        $this->stuff = $named;

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

    public function retrieve(): Nameable
    {
        $data = $this->stuff->getNameableCopy();

        return $data;
    }

    public function has(): bool
    {
        return !is_null($this->stuff);
    }

    private function getLocation(): StorageLocation
    {
        $repository = new StorageLocation(
            $this->storageLocation,
            $this->db,
        );
        return $repository;
    }

    private function getSource(): DataSource
    {
        $repository = new DataSource($this->dataSource, $this->db);
        return $repository;
    }
}
