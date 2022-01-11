<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 3:09
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
    public function filterData(string $code, $filters = []): array
    {
        $schema = new Category($this->db, $code);
        $installation = $schema->getInstance();
        $seeker = new Seeker($installation);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $data = $seeker->data($filters);

        return $data;
    }

    /**
     * @throws Exception
     */
    public function filters(string $code): array
    {
        $schema = new Category($this->db, $code);
        $source = $schema->getInstance();
        $seeker = new Seeker($source);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $filters = $seeker->filters();

        return $filters;
    }
}