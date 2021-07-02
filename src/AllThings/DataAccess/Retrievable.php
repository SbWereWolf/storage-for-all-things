<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace AllThings\DataAccess;


interface Retrievable
{
    /**
     * Получит данные
     * @return mixed
     */
    public function retrieveData();

    /** Данные имеются ?
     * @return bool
     */
    public function has(): bool;

}
