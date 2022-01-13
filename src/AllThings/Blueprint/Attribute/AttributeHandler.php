<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 9:03
 */

namespace AllThings\Blueprint\Attribute;

use AllThings\DataAccess\Uniquable\UniqueHandler;
use PDO;

class AttributeHandler
    extends UniqueHandler
    implements IAttributeHandler
{
    private ?IAttribute $stuff = null;

    public function __construct(
        string $uniqueness,
        string $locationName,
        PDO $db,
    ) {
        parent::__construct($uniqueness, $locationName, $db);

        $this->dataSource = $locationName;
    }

    public function setAttribute(
        IAttribute $stuff
    ): IAttributeHandler {
        $this->stuff = $stuff;

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

    public function retrieve(): IAttribute
    {
        $result = $this->stuff->GetAttributeCopy();

        return $result;
    }

    public function has(): bool
    {
        return !is_null($this->stuff);
    }

    private function getSource(): AttributeSource
    {
        $repository = new AttributeSource(
            $this->dataSource,
            $this->db
        );

        return $repository;
    }

    private function getLocation(): AttributeLocation
    {
        $repository = new AttributeLocation(
            $this->storageLocation,
            $this->db
        );
        return $repository;
    }
}
