<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Uniquable;

interface UniquableManager
{
    public function create(string $uniqueness): bool;

    public function remove(string $uniqueness): bool;

    public function properties(array $entities, array $fields): array;

    public function setLocation(
        string $storageLocation
    ): UniquableManager;

    public function setSource(
        string $dataSource
    ): UniquableManager;

    public function setUniqueIndex(
        string $uniqueIndex
    ): UniqueManager;
}