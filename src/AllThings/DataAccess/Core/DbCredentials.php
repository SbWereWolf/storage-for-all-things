<?php

namespace AllThings\DataAccess\Core;

class DbCredentials implements IDbCredentials
{
    private $options;

    private function __construct(string $path)
    {
        $this->options = include($path);
    }

    /**
     * @return array
     */
    public static function getReaderCredentials():array
    {
        $dbReadCredentials = new self(DB_READ_CONFIGURATION);
        $readerCredentials = $dbReadCredentials->getPdoAttributes();

        return $readerCredentials;
    }

    /**
     * @return array
     */
    private function getPdoAttributes():array
    {
        $pdoDriver = $this->options[IDbCredentials::PDO_DBMS];
        $dbName = $this->options[IDbCredentials::DB_NAME];
        $dbHost = $this->options[IDbCredentials::DB_HOST];
        $dbAddress = $pdoDriver
            . ':'
            . self::DB_NAME_PARAMETER
            . '='
            . $dbName
            . ';'
            . self::DB_HOST_PARAMETER
            . '='
            . $dbHost;

        $dbLogin = $this->options[IDbCredentials::DB_LOGIN];
        $dbPassword = $this->options[IDbCredentials::DB_PASSWORD];

        $credentials[IDbCredentials::DATA_SOURCE_NAME] = $dbAddress;
        $credentials[IDbCredentials::LOGIN] = $dbLogin;
        $credentials[IDbCredentials::PASSWORD] = $dbPassword;

        return $credentials;
    }

    /**
     * @return array
     */
    public static function getWriterCredentials():array
    {
        $dbWriteCredentials = new self(DB_WRITE_CONFIGURATION);
        $writerCredentials = $dbWriteCredentials->getPdoAttributes();

        return $writerCredentials;
    }
}


