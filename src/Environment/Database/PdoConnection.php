<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 2022-12-29
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

    public function get(array $options = []): PDO
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
            $options
        );

        return $connection;
    }
}
