<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 3:25
 */

namespace AllThings\ControlPanel;


use AllThings\SearchEngine\Seeker;
use AllThings\StorageEngine\StorageManager;
use Exception;
use PDO;

class Browser
{
    private PDO $db;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    /**
     * @throws Exception
     */
    public function filters(string $essence): array
    {
        $manager = new StorageManager($this->db, $essence);
        $dataHandler = $manager->getHandler();

        $seeker = new Seeker($dataHandler);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $filters = $seeker->limits();

        return $filters;
    }

    /**
     * @throws Exception
     */
    public function find(string $essence, $filters = []): array
    {
        $manager = new StorageManager($this->db, $essence);
        $dataHandler = $manager->getHandler();

        $seeker = new Seeker($dataHandler);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $data = $seeker->seek($filters);

        return $data;
    }
}