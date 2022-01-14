<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 6:42
 */

namespace AllThings\ControlPanel;

use AllThings\DataAccess\Linkage\Linkage;
use AllThings\DataAccess\Linkage\LinkageManager;
use Exception;

class EssenceRelated
{
    private string $essence;
    private LinkageManager $relation;

    /**
     * @param string         $essence
     * @param LinkageManager $relation
     */
    public function __construct(
        string $essence,
        LinkageManager $relation,
    ) {
        $this->essence = $essence;
        $this->relation = $relation;
    }

    public function attach(string $related): static
    {
        $linkage = (new Linkage())
            ->setLeftValue($this->essence)
            ->setRightValue($related);

        $isSuccess = $this->relation->attach($linkage);

        if (!$isSuccess) {
            throw new Exception(
                "Related `$related` must be attached with success"
            );
        }

        return $this;
    }

    public function detach(string $related): static
    {
        $linkage = (new Linkage())
            ->setLeftValue($this->essence)
            ->setRightValue($related);

        $isSuccess = $this->relation->detach($linkage);

        if (!$isSuccess) {
            throw new Exception(
                "Related `$related` must be detached with success"
            );
        }

        return $this;
    }

    public function purge(): static
    {
        $linkage = (new Linkage())
            ->setLeftValue($this->essence);

        $isSuccess = $this->relation->detach($linkage);

        if (!$isSuccess) {
            throw new Exception(
                'All Related must be detached with success'
            );
        }

        return $this;
    }

    public function list(): array
    {
        $linkage = (new Linkage())
            ->setLeftValue($this->essence);

        $result = $this->relation->getAssociated($linkage);

        return $result;
    }
}