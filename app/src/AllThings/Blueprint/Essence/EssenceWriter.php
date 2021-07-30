<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */

namespace AllThings\Blueprint\Essence;


interface EssenceWriter
{

    public function insert(IEssence $entity): bool;

    public function setIsHidden(IEssence $entity): bool;

    public function update(IEssence $target_entity, IEssence $suggestion_entity): bool;
}
