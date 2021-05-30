<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13.01.2017
 * Time: 21:07
 */

namespace Environment;


interface IDbCredentials
{
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
