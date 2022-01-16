<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Nameable;

use AllThings\Blueprint\Essence\IEssence;
use AllThings\DataAccess\Uniquable\UniqueHandler;
use Exception;

class NamedHandler extends UniqueHandler implements ValuableHandler
{
    /**
     * @throws Exception
     */
    public function read(string $uniqueness): Nameable
    {
        $source = $this->getSource($uniqueness);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $source->select();

        return $result;
    }

    /**
     * @param object $named
     *
     * @return bool
     * @throws Exception
     */
    public function write(object $named): bool
    {
        if (!($named instanceof Nameable)) {
            $className = IEssence::class;
            throw new Exception("Arg \$named MUST BE `$className`");
        }
        $location = $this->getLocation($named->getCode());
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $location->update($named);

        return $result;
    }

    /** @noinspection PhpPureAttributeCanBeAddedInspection */
    private function getLocation(
        string $uniqueness,
    ): StorageLocation {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $repository = new StorageLocation(
            $this->db,
            $this->storageLocation,
            $uniqueness,
            $this->uniqueIndex,
        );

        return $repository;
    }

    /** @noinspection PhpPureAttributeCanBeAddedInspection */
    private function getSource(string $uniqueness): DataSource
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $repository = new DataSource(
            $this->db,
            $this->dataSourceName,
            $uniqueness,
            $this->uniqueIndex,
        );

        return $repository;
    }
}
