<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 01.10.2019, 16:52
 */

namespace AllThings\LanguageFeatures;


class ArrayParser
{
    private $data = array();

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getInteger(string $key): int
    {

        $value = (int)($this->safely($key));
        return $value;
    }

    public function safely(string $key)
    {

        $data = $this->data;
        $isExists = array_key_exists($key, $data);

        $value = null;
        if ($isExists) {
            $value = $data[$key];
        }
        return $value;
    }

    public function getFloat(string $key): float
    {

        $value = (float)($this->safely($key));
        return $value;
    }

    public function getString(string $key): string
    {

        $value = (string)($this->safely($key));
        return $value;
    }

    public function getBoolean(string $key): string
    {

        $value = (bool)($this->safely($key));
        return $value;
    }

    public function reduceNesting(): array
    {
        $reduced = [];
        foreach ($this->data as $nestedArray) {
            $reduced[] = current($nestedArray);

        }
        return $reduced;
    }

}
