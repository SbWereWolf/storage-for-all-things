<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 17.01.2022, 7:56
 */

namespace AllThings\StorageEngine;

use JetBrains\PhpStorm\Pure;
use PDO;

class DBObject
{
    protected const STRUCTURE_PREFIX = 'auto_';
    /**
     * @var PDO
     */
    protected PDO $db;
    private string $essence;

    public function __construct(string $essence, PDO $linkToData)
    {
        $this->essence = $essence;
        $this->db = $linkToData;
    }

    /**
     * @return PDO
     */
    public function getDb(): PDO
    {
        return $this->db;
    }

    #[Pure]
    public function name(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $name = static::STRUCTURE_PREFIX . $this->getEssence();

        return $name;
    }

    /**
     * @return string
     */
    public function getEssence(): string
    {
        return $this->essence;
    }
}