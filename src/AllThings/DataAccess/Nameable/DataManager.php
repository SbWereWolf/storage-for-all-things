<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:45
 */

namespace AllThings\DataAccess\Nameable;


interface DataManager
{

    public function create(): bool;

    public function remove(): bool;

    public function correct(string $targetIdentity = ''): bool;

    public function browse(): bool;
}
