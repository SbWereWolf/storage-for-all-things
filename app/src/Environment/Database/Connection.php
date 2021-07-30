<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */

namespace Environment\Database;


use PDO;

interface Connection
{

    public function getForWrite(): PDO;

    public function getForRead(): PDO;

    public function getForDelete(): PDO;
}
