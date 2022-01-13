<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 3:25
 */

namespace AllThings\ControlPanel;

use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Crossover\ICrossoverManager;
use AllThings\DataAccess\Linkage\Linkage;
use Exception;

class Specification
{
    private string $thing;
    private ContentAccessFactory $factory;

    public function __construct(
        string $thing,
        ContentAccessFactory $accessFactory,
    ) {
        $this->thing = $thing;
        $this->factory = $accessFactory;
    }

    /**
     * @throws Exception
     */
    public function attach(array $definition): static
    {
        foreach ($definition as $attribute) {
            $this->attachOne($attribute);
        }

        return $this;
    }

    private function attachOne(string $attribute): static
    {
        $linkage = (new Linkage())
            ->setLeftValue($this->thing)
            ->setRightValue($attribute);
        $content = $this->factory->makeContentAccess($attribute);

        $isSuccess = $content->attach($linkage);
        if (!$isSuccess) {
            throw new Exception(
                'Value must be attached with success'
            );
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function detach(array $definition): static
    {
        foreach ($definition as $attribute) {
            $this->detachOne($attribute);
        }

        return $this;
    }

    private function detachOne(string $attribute): static
    {
        $linkage = (new Linkage())
            ->setLeftValue($this->thing)
            ->setRightValue($attribute);
        $content = $this->factory->makeContentAccess($attribute);

        $isSuccess = $content->detach($linkage);
        if (!$isSuccess) {
            throw new Exception(
                'Value must be detached with success'
            );
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function define(array $definition): static
    {
        foreach ($definition as $attribute => $content) {
            $this->defineOne($attribute, $content);
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    private function defineOne(
        string $attribute,
        string $content
    ): static {
        $value = (new Crossover())
            ->setContent($content);
        $value->setLeftValue($this->thing)
            ->setRightValue($attribute);

        $access = $this->factory->makeContentAccess($attribute);
        $access->setSubject($value);

        $isSuccess = $access->store($value);
        if (!$isSuccess) {
            throw new Exception(
                'Value must be defined with success'
            );
        }

        return $this;
    }

    public function purge(array $attributes): static
    {
        $linkage = (new Crossover())
            ->setLeftValue($this->thing);

        $accesses = $this->factory->makeAllAccess($attributes);
        foreach ($accesses as $access) {
            /** @var ICrossoverManager $access */
            $isSuccess = $access->detach($linkage);
            if (!$isSuccess) {
                throw new Exception(
                    'All Attribute must be detached with success'
                );
            }
        }

        return $this;
    }
}