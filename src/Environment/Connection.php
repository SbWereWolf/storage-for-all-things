<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 12:24
 */

namespace Environment;


use PDO;

interface Connection
{

    function getForWrite(): PDO;

    function getForRead(): PDO;

    function getForDelete(): PDO;
}
