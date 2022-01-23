<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 23.01.2022, 13:07
 */

namespace AllThings\DataAccess\Common;

class ColumnManager
{
    private array $columnValues;

    public function __construct(array $columnValues)
    {
        $this->columnValues = $columnValues;
    }

    /**
     * @param bool|array $data
     * @param string $index
     * @return array
     */
    public function indexWith(string $index): array
    {
        $data = $this->columnValues;
        $data = array_column($data, null, $index);
        foreach ($data as $key => $val) {
            unset($data[$key][$index]);
        }
        return $data;
    }
}