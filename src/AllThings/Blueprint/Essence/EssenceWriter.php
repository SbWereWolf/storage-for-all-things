<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\Blueprint\Essence;

use AllThings\DataAccess\Uniquable\UniquableWriter;

interface EssenceWriter extends UniquableWriter
{
    public function update(IEssence $suggestion): bool;
}
