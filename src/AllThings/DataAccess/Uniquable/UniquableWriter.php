<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Uniquable;

interface UniquableWriter
{
    public function insert(): bool;

    public function delete(): bool;
}