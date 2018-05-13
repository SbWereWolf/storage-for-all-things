<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 13:01
 */

namespace AllThings\DataAccess\Core;


class DbConnection implements Connection
{

    private $dataSource = '';
    private $dbLogin = '';
    private $dbPassword = '';

    function getForWrite(): \PDO
    {
        $dbCredentials = DbCredentials::getWriterCredentials();

        $connection = $this->getDbConnection($dbCredentials);

        return $connection;
    }

    function getForRead(): \PDO
    {
        $dbCredentials = DbCredentials::getReaderCredentials();

        $connection = $this->getDbConnection($dbCredentials);

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
     * @return \PDO
     */
    private function getPdoConnection(): \PDO
    {
        $connection = new \PDO ($this->dataSource,
            $this->dbLogin,
            $this->dbPassword);
        return $connection;
    }

    private function getDbConnection($dbCredentials): \PDO
    {
        $this->setProperties($dbCredentials);
        $connection = $this->getPdoConnection();

        return $connection;
    }
}
