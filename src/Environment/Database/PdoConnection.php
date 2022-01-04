<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 04.01.2022, 16:48
 */

namespace Environment\Database;


use PDO;
use RuntimeException;

class PdoConnection implements IPdoConnecting
{
    private EnvReader $reader;

    public function __construct(string $path)
    {
        $this->reader = new EnvReader($path);
    }

    public function get(): PDO
    {
        $variables = $this->reader->read();
        if (!key_exists(static::DSN, $variables)) {
            throw new RuntimeException(
                'Missing environment variable DSN'
            );
        }

        $connection = new PDO (
            $variables[static::DSN],
            $variables[static::LOGIN] ?? '',
            $variables[static::PASSWORD] ?? '',
        );

        return $connection;
    }
}
