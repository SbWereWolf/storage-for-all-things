<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 03.07.2021, 8:12
 */

namespace Environment\Database;


use PDO;

class DbConnection implements Connection
{

    private $dataSource = '';
    private $dbLogin = '';
    private $dbPassword = '';

    public function getForWrite(): PDO
    {
        $dbCredentials = DbCredentials::getWriterCredentials();

        $connection = $this->getDbConnection($dbCredentials);

        return $connection;
    }

    private function getDbConnection($dbCredentials): PDO
    {
        $this->setProperties($dbCredentials);
        $connection = $this->getPdoConnection();

        return $connection;
    }

    /**
     * @param $dbCredentials
     * @return bool
     */
    private function setProperties(array $dbCredentials): bool
    {
        $this->dataSource = $dbCredentials[IDbCredentials::DATA_SOURCE_NAME];
        $this->dbLogin = $dbCredentials[IDbCredentials::LOGIN];
        $this->dbPassword = $dbCredentials[IDbCredentials::PASSWORD];

        return true;
    }

    /**
     * @return PDO
     */
    private function getPdoConnection(): PDO
    {
        $connection = new PDO (
            $this->dataSource,
            $this->dbLogin,
            $this->dbPassword
        );
        return $connection;
    }

    public function getForDelete(): PDO
    {
        $dbCredentials = DbCredentials::getDeleteCredentials();

        $connection = $this->getDbConnection($dbCredentials);

        return $connection;
    }

    public function getForRead(): PDO
    {
        $dbCredentials = DbCredentials::getReaderCredentials();

        $connection = $this->getDbConnection($dbCredentials);

        return $connection;
    }
}
