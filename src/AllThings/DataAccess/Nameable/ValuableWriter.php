<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:45
 */

namespace AllThings\DataAccess\Nameable;


interface ValuableWriter
{

    public function insert(Nameable $entity): bool;

    public function setIsHidden(Nameable $entity): bool;

    public function update(Nameable $target_entity, Nameable $suggestion_entity): bool;
}
