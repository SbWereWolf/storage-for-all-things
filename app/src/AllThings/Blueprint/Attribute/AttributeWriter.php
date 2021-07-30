<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */

namespace AllThings\Blueprint\Attribute;


interface AttributeWriter
{

    public function insert(IAttribute $entity): bool;

    public function setIsHidden(IAttribute $entity): bool;

    public function update(IAttribute $target_entity, IAttribute $suggestion_entity): bool;
}
