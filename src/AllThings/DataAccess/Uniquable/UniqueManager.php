<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 4/10/22, 2:45 PM
 */

namespace AllThings\DataAccess\Uniquable;

use Exception;
use PDO;

class UniqueManager implements UniquableManager
{
    protected PDO $db;
    protected string $storageLocation;
    protected string $dataSource;
    protected string $uniqueIndex;

    /**
     * @param PDO $db
     * @param string $storageLocation
     * @param string $dataSource
     * @param string $uniqueColumn
     */
    public function __construct(
        PDO $db,
        string $storageLocation = '',
        string $dataSource = '',
        string $uniqueColumn = 'code',
    ) {
        $this->db = $db;
        $this->storageLocation = $storageLocation;
        $this->dataSource = $dataSource;
        $this->uniqueIndex = $uniqueColumn;
    }

    /**
     * @throws Exception
     */
    public function create(string $uniqueness): bool
    {
        $handler = $this->getUniqueHandler();
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $handler->add($uniqueness);

        return $result;
    }

    /**
     * @throws Exception
     */
    public function remove(string $uniqueness): bool
    {
        $handler = $this->getUniqueHandler();
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $handler->erase($uniqueness);

        return $result;
    }

    /**
     * @throws Exception
     */
    public function properties(array $entities, array $fields): array
    {
        $handler = $this->getUniqueHandler();
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $handler->take($fields, $entities);

        return $result;
    }

    /**
     * @param string $storageLocation
     *
     * @return UniqueManager
     */
    public function setLocation(
        string $storageLocation
    ): UniquableManager {
        $this->storageLocation = $storageLocation;

        return $this;
    }

    /**
     * @param string $dataSource
     *
     * @return UniqueManager
     */
    public function setSource(
        string $dataSource
    ): UniquableManager {
        $this->dataSource = $dataSource;

        return $this;
    }

    /**
     * @param string $uniqueIndex
     *
     * @return UniqueManager
     */
    public function setUniqueness(
        string $uniqueIndex
    ): UniqueManager {
        $this->uniqueIndex = $uniqueIndex;

        return $this;
    }

    /**
     * @return UniqueHandler
     * @throws Exception
     */
    private function getUniqueHandler(): UniqueHandler
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $handler = new UniqueHandler(
            $this->db,
            $this->storageLocation,
            $this->dataSource,
            $this->uniqueIndex,
        );
        return $handler;
    }
}
