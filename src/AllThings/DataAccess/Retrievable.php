<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 20.11.2021, 13:45
 */

namespace AllThings\DataAccess;


interface Retrievable
{
    /**
     * Получить данные
     * @return mixed
     */
    public function retrieveData();

    /** Данные имеются ?
     * @return bool
     */
    public function has(): bool;

}
