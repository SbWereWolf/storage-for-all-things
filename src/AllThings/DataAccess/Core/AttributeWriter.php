<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:44
 */

namespace AllThings\DataAccess\Core;


use AllThings\Attribute\IAttribute;

interface AttributeWriter
{

    public function insert(IAttribute $entity): bool;

    public function setIsHidden(IAttribute $entity): bool;

    public function update(IAttribute $target_entity, IAttribute $suggestion_entity): bool;
}
