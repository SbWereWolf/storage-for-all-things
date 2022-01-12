<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 17:50
 */

namespace AllThings\DataAccess;


interface Extractable
{
    /**
     * Получить данные
     *
     * @return mixed
     */
    public function extract(): array;
}
