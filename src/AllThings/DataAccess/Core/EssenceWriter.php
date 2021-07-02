<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:44
 */

namespace AllThings\DataAccess\Core;


use AllThings\Attribute\IEssence;

interface EssenceWriter
{

    public function insert(IEssence $entity): bool;

    public function setIsHidden(IEssence $entity): bool;

    public function update(IEssence $target_entity, IEssence $suggestion_entity): bool;
}
