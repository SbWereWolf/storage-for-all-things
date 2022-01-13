<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 3:02
 */

namespace Environment\Database;

use InvalidArgumentException;
use RuntimeException;

class EnvReader implements EnvReading
{

    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException(
                "The file `$path` does not exist"
            );
        }
        if (!is_readable($path)) {
            throw new RuntimeException(
                "The file `$path` is not readable"
            );
        }
        $this->path = $path;
    }

    public function read(): array
    {
        $lines = file(
            $this->path,
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );
        $result = [];
        foreach ($lines as $line) {
            $line = trim($line);
            $comment = strpos($line, '#');
            if ($comment !== false) {
                $line = substr($line, 0, $comment);
            }
            $assign = strpos($line, '=');
            if ($assign !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                $result[$name] = $value;
            }
        }

        return $result;
    }
}