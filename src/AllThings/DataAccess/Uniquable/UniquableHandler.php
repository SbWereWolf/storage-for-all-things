<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Uniquable;

interface UniquableHandler
{
    public function add(string $uniqueness): bool;

    public function erase(string $uniqueness): bool;

    public function take(array $fields, array $uniquenesses): array;
}
