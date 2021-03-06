<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:22
 */

namespace AllThings\SearchEngine;


class Filter implements Filtering
{
    /**
     * @var string
     */
    private $attribute;

    public function __construct(string $attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * @return string
     */
    public function getAttribute(): string
    {
        return $this->attribute;
    }

    /**
     * @param string $attribute
     *
     * @return Filter
     */
    private function setAttribute(string $attribute): Filter
    {
        $this->attribute = $attribute;
        return $this;
    }

}
