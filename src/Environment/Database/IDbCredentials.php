<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:45
 */

namespace Environment\Database;


interface IDbCredentials
{
    public const DB_DELETE_CONFIGURATION =
        __DIR__
        . DIRECTORY_SEPARATOR
        . '..'
        . DIRECTORY_SEPARATOR
        . '..'
        . DIRECTORY_SEPARATOR
        . '..'
        . DIRECTORY_SEPARATOR
        . 'configuration'
        . DIRECTORY_SEPARATOR
        . 'db_test.php';
    public const DB_WRITE_CONFIGURATION =
        __DIR__
        . DIRECTORY_SEPARATOR
        . '..'
        . DIRECTORY_SEPARATOR
        . '..'
        . DIRECTORY_SEPARATOR
        . '..'
        . DIRECTORY_SEPARATOR
        . 'configuration'
        . DIRECTORY_SEPARATOR
        . 'db_test.php';
    public const DB_READ_CONFIGURATION =
        __DIR__
        . DIRECTORY_SEPARATOR
        . '..'
        . DIRECTORY_SEPARATOR
        . '..'
        . DIRECTORY_SEPARATOR
        . '..'
        . DIRECTORY_SEPARATOR
        . 'configuration'
        . DIRECTORY_SEPARATOR
        . 'db_test.php';

    public const PDO_DSN_PREFIX = 'DSN_PREFIX';
    public const DATA_SOURCE_NAME = 'DATA_SOURCE_NAME';
    public const LOGIN = 'DB_USER_LOGIN';
    public const PASSWORD = 'DB_USER_PASSWORD';

    public const DB_NAME_PARAMETER = 'dbname';
    public const DB_HOST_PARAMETER = 'host';

    public const DB_HOST = 'DB_HOST';
    public const DB_PORT = 'DB_PORT';
    public const DB_LOGIN = 'DB_LOGIN';
    public const DB_PASSWORD = 'DB_PASSWORD';
    public const DB_NAME = 'DB_NAME';
    public const PDO_DBMS = 'PDO_DBMS';

    public static function getReaderCredentials(): array;

    public static function getWriterCredentials(): array;

    public static function getDeleteCredentials(): array;
}
