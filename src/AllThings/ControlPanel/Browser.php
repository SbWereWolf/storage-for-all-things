<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 3:54
 */

namespace AllThings\ControlPanel;


use AllThings\SearchEngine\Seeker;
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
        $category = new Category($this->db, $essence);
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
        $category = new Category($this->db, $essence);
        $dataHandler = $category->getHandler();

        $seeker = new Seeker($dataHandler);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $data = $seeker->seek($filters);

        return $data;
    }
}