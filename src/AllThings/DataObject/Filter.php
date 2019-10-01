<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 01.10.2019, 20:31
 */

namespace AllThings\DataObject;


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
