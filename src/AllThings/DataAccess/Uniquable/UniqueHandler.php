<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Uniquable;

use Exception;
use PDO;

class UniqueHandler implements UniquableHandler
{
    protected PDO $db;
    protected string $storageLocation;
    protected string $dataSourceName = '';
    protected string $uniqueIndex;

    /**
     * @param PDO    $db
     * @param string $storageLocationName
     * @param string $dataSourceName
     * @param string $uniqueIndex
     *
     * @throws Exception
     */
    public function __construct(
        PDO $db,
        string $storageLocationName = '',
        string $dataSourceName = '',
        string $uniqueIndex = 'code',
    ) {
        $this->db = $db;
        if (!$storageLocationName && !$dataSourceName) {
            throw new Exception(
                "One of STORAGE LOCATION `$storageLocationName`" .
                " or DATA SOURCE `$dataSourceName` MUST BE defined"
            );
        }
        if (!$storageLocationName && $dataSourceName) {
            $storageLocationName = $dataSourceName;
        }
        if ($storageLocationName && !$dataSourceName) {
            $dataSourceName = $storageLocationName;
        }
        $this->storageLocation = $storageLocationName;
        $this->dataSourceName = $dataSourceName;
        $this->uniqueIndex = $uniqueIndex;
    }

    public function add(string $uniqueness): bool
    {
        $location = new UniqueLocation(
            $this->db,
            $this->storageLocation,
            $uniqueness,
            $this->uniqueIndex,
        );
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $location->insert();

        return $result;
    }

    public function erase(string $uniqueness): bool
    {
        $location = new UniqueLocation(
            $this->db,
            $this->storageLocation,
            $uniqueness,
            $this->uniqueIndex,
        );
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $location->delete();

        return $result;
    }

    public function take(array $fields, array $uniquenesses): array
    {
        $source = new UniqueSource(
            $this->db,
            $this->dataSourceName,
            $uniquenesses,
            $this->uniqueIndex,
        );
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $source->select($fields);

        return $result;
    }
}