<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 4/10/22, 2:45 PM
 */

namespace AllThings\ControlPanel;

use AllThings\Blueprint\Relation\ContentAccessFactory;
use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Crossover\ICrossoverManager;
use AllThings\DataAccess\Linkage\Linkage;
use Exception;

class Specification
{
    private string $thing;
    private ContentAccessFactory $factory;

    /**
     * @param string               $thing
     * @param ContentAccessFactory $accessFactory
     */
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
    public function attach(array $definition): bool
    {
        $isSuccess = true;
        foreach ($definition as $attribute) {
            $isSuccess = $isSuccess && $this->attachOne($attribute);
        }

        return $isSuccess;
    }

    /**
     * @throws Exception
     */
    private function attachOne(string $attribute): bool
    {
        $linkage = (new Linkage())
            ->setLeftValue($this->thing)
            ->setRightValue($attribute);
        $content = $this->factory->makeContentAccess($attribute);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $isSuccess = $content->attach($linkage);
        /*        if (!$isSuccess) {
                    throw new Exception(
                        'Value must be attached with success'
                    );
                }*/

        return $isSuccess;
    }

    /**
     * @throws Exception
     */
    public function detach(array $definition): bool
    {
        $isSuccess = true;
        foreach ($definition as $attribute) {
            $isSuccess = $isSuccess && $this->detachOne($attribute);
        }

        return $isSuccess;
    }

    /**
     * @throws Exception
     */
    private function detachOne(string $attribute): bool
    {
        $linkage = (new Linkage())
            ->setLeftValue($this->thing)
            ->setRightValue($attribute);
        $content = $this->factory->makeContentAccess($attribute);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $isSuccess = $content->detach($linkage);
        /*        if (!$isSuccess) {
                    throw new Exception(
                        'Value must be detached with success'
                    );
                }*/

        return $isSuccess;
    }

    /**
     * @throws Exception
     */
    public function define(array $definition): bool
    {
        $isSuccess = true;
        foreach ($definition as $attribute => $content) {
            $isSuccess = $isSuccess &&
                $this->defineOne($attribute, $content);
        }

        return $isSuccess;
    }

    /**
     * @throws Exception
     */
    private function defineOne(
        string $attribute,
        string $content
    ): bool {
        $value = (new Crossover())
            ->setContent($content);
        $value->setLeftValue($this->thing)
            ->setRightValue($attribute);

        $access = $this->factory->makeContentAccess($attribute);
        $access->setSubject($value);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $isSuccess = $access->store($value);
        /*        if (!$isSuccess) {
                    throw new Exception(
                        'Value must be defined with success'
                    );
                }*/

        return $isSuccess;
    }

    /**
     * @throws Exception
     */
    public function purge(array $attributes): bool
    {
        $linkage = (new Crossover())
            ->setLeftValue($this->thing);

        $accesses = $this->factory->makeAllAccess($attributes);
        $isSuccess = true;
        foreach ($accesses as $access) {
            /** @var ICrossoverManager $access */
            $isSuccess = $isSuccess && $access->detach($linkage);
            /*            if (!$isSuccess) {
                            throw new Exception(
                                'All Attribute must be detached with success'
                            );
                        }*/
        }

        return $isSuccess;
    }
}