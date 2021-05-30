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

    public function getForWrite(): PDO;

    public function getForRead(): PDO;

    public function getForDelete(): PDO;
}
