<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 16:28
 */

namespace Environment\Presentation;


class FromEssenceAttribute implements Jsonable
{
    private $dataSet = [];

    public function __construct($dataSet)
    {
        $this->dataSet = $dataSet;
    }

    public function toJson(): string
    {
        $json = json_encode($this->dataSet);

        return $json;
    }
}
