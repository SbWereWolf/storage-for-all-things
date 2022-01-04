<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\DataAccess\Uniquable;

interface UniquableWriter
{
    public function insert(string $uniqueness): bool;

    public function delete(string $uniqueness): bool;
}