<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13.01.2017
 * Time: 21:07
 */

namespace AllThings\DataAccess\Core;


interface IDbCredentials
{
    const PDO_DSN_PREFIX = 'DSN_PREFIX';
    const DATA_SOURCE_NAME = 'DATA_SOURCE_NAME';
    const LOGIN = 'DB_USER_LOGIN';
    const PASSWORD = 'DB_USER_PASSWORD';

    const DB_NAME_PARAMETER = 'dbname';
    const DB_HOST_PARAMETER = 'host';

    const DB_HOST = 'DB_HOST';
    const DB_PORT = 'DB_PORT';
    const DB_LOGIN = 'DB_LOGIN';
    const DB_PASSWORD = 'DB_PASSWORD';
    const DB_NAME = 'DB_NAME';
    const PDO_DBMS = 'PDO_DBMS';

    public static function getReaderCredentials():array;

    public static function getWriterCredentials():array;
}
