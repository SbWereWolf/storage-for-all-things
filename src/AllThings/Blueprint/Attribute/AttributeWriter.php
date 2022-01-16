<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\Blueprint\Attribute;

use AllThings\DataAccess\Uniquable\UniquableWriter;

interface AttributeWriter extends UniquableWriter
{
    public function update(IAttribute $suggestion): bool;
}
