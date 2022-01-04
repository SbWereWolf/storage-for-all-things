<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\DataAccess\Nameable;


interface ValuableWriter
{
    public function update(
        Nameable $target_entity,
        Nameable $suggestion_entity,
    ): bool;
}
