<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 17:50
 */

namespace AllThings\ControlPanel;

use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Linkage\Linkage;
use AllThings\DataAccess\Linkage\LinkageManager;
use Exception;

class Category
{
    private string $essence;
    private LinkageManager $category;

    /**
     * @param string         $essence
     * @param LinkageManager $category
     */
    public function __construct(
        string $essence,
        LinkageManager $category,
    ) {
        $this->essence = $essence;
        $this->category = $category;
    }

    public function attach(string $attribute): static
    {
        $linkage = (new Linkage())
            ->setLeftValue($this->essence)
            ->setRightValue($attribute);

        $isSuccess = $this->category->attach($linkage);

        if (!$isSuccess) {
            throw new Exception(
                'Attribute must be attached with success'
            );
        }

        return $this;
    }

    public function detach(string $attribute): static
    {
        $linkage = (new Crossover())
            ->setLeftValue($this->essence)
            ->setRightValue($attribute);

        $isSuccess = $this->category->detach($linkage);

        if (!$isSuccess) {
            throw new Exception(
                'Attribute must be detached with success'
            );
        }

        return $this;
    }

    public function purge(): static
    {
        $linkage = (new Crossover())
            ->setLeftValue($this->essence);

        $isSuccess = $this->category->detach($linkage);

        if (!$isSuccess) {
            throw new Exception(
                'All Attribute must be detached with success'
            );
        }

        return $this;
    }

    public function list(): array
    {
        $linkage = (new Crossover())
            ->setLeftValue($this->essence);

        $result = $this->category->getAssociated($linkage);

        return $result;
    }
}