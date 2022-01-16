<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Linkage;

interface IForeignKey
{
    public function getTable(): string;

    public function getPrimaryIndex(): string;

    public function getMatchColumn(): string;
}
