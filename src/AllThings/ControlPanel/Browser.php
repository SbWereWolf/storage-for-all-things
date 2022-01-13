<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 9:02
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
        $category = new StorageManager($this->db, $essence);
        $dataHandler = $category->getHandler();

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
        $category = new StorageManager($this->db, $essence);
        $dataHandler = $category->getHandler();

        $seeker = new Seeker($dataHandler);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $data = $seeker->seek($filters);

        return $data;
    }
}