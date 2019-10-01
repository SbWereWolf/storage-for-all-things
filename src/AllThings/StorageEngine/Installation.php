<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 01.10.2019, 0:39
 */

namespace AllThings\StorageEngine;


interface Installation
{
    public function setup(): bool;

    public function name(): string;
}
