<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 04.01.2022, 16:48
 */

namespace Environment\Database;


use PDO;

interface IPdoConnecting
{
    public const DSN = 'ALL_THING_DSN';
    public const LOGIN = 'ALL_THING_LOGIN';
    public const PASSWORD = 'ALL_THING_PASSWORD';

    public function get(): PDO;
}
