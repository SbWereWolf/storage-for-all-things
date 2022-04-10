<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 4/10/22, 2:45 PM
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

    public function setUniqueness(
        string $uniqueIndex
    ): UniqueManager;
}