<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 6:19
 */

namespace AllThings\Interaction;

class ContentFactory
{
    private string $feature = '';
    private string $product = '';
    private string $content = '';

    public function make()
    {
        $content = new Content(
            $this->product,
            $this->feature,
            $this->content,
        );

        return $content;
    }

    /**
     * @param string $feature
     *
     * @return ContentFactory
     */
    public function setFeature(string $feature): ContentFactory
    {
        $this->feature = $feature;
        return $this;
    }

    /**
     * @param string $product
     *
     * @return ContentFactory
     */
    public function setProduct(string $product): ContentFactory
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @param string $content
     *
     * @return ContentFactory
     */
    public function setContent(string $content): ContentFactory
    {
        $this->content = $content;
        return $this;
    }

}