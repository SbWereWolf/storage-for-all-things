<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
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
        $schema = new Schema($this->db, $code);
        $installation = $schema->getInstallation();
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
        $schema = new Schema($this->db, $code);
        $source = $schema->getInstallation();
        $seeker = new Seeker($source);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $filters = $seeker->filters();

        return $filters;
    }
}