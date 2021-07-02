<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\Blueprint\Essence;


interface EssenceWriter
{

    public function insert(IEssence $entity): bool;

    public function setIsHidden(IEssence $entity): bool;

    public function update(IEssence $target_entity, IEssence $suggestion_entity): bool;
}
