<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\Blueprint\Essence;

use AllThings\DataAccess\Uniquable\UniquableWriter;

interface EssenceWriter extends UniquableWriter
{
    public function update(
        IEssence $suggestion,
        string $target = '',
    ): bool;
}
