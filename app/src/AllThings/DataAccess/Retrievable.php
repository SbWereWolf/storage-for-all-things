<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
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
