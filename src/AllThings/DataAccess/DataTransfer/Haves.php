<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 9:02
 */

namespace AllThings\DataAccess\DataTransfer;


interface Haves
{
    /** Данные имеются ?
     *
     * @return bool
     */
    public function has(): bool;

}
