<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 9:02
 */

namespace AllThings\Blueprint\Attribute;

use AllThings\DataAccess\Uniquable\UniquableWriter;

interface AttributeWriter extends UniquableWriter
{
    public function update(IAttribute $suggestion, string $target): bool;
}
