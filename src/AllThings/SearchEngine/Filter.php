<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:45
 */

namespace AllThings\SearchEngine;


class Filter implements Filtering
{
    /**
     * @var string
     */
    private string $attribute;

    public function __construct(string $attribute)
    {
        $this->setAttribute($attribute);
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
    private function setAttribute(string $attribute): self
    {
        $this->attribute = $attribute;
        return $this;
    }

}
