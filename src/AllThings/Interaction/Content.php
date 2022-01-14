<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 6:19
 */

namespace AllThings\Interaction;

class Content
{
    private string $product;
    private string $feature;
    private string $content;

    public function __construct(
        string $product,
        string $feature,
        string $content,
    ) {
        $this->product = $product;
        $this->feature = $feature;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getFeature(): string
    {
        return $this->feature;
    }

    /**
     * @return string
     */
    public function getProduct(): string
    {
        return $this->product;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}