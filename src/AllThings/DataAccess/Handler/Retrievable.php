<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 10.11.19 3:46
 */

/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 17:08
 */

namespace AllThings\DataAccess\Handler;


interface Retrievable
{
    /**
     * Получит данные
     * @return mixed
     */
    function retrieveData();

    /** Данные имеются ?
     * @return bool
     */
    function has():bool;

}
