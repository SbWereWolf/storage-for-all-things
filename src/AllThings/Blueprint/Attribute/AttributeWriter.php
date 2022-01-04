<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\Blueprint\Attribute;

use AllThings\DataAccess\Uniquable\UniquableWriter;

interface AttributeWriter extends UniquableWriter
{
    public function update(IAttribute $target_entity, IAttribute $suggestion_entity): bool;
}
