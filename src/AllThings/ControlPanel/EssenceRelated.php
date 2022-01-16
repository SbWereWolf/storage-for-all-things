<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
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

    /**
     * @throws Exception
     */
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

    /**
     * @throws Exception
     */
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

    /**
     * @throws Exception
     */
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

    public function list(array $fields = []): array
    {
        $linkage = (new Linkage())
            ->setLeftValue($this->essence);

        if (!$fields) {
            $result = $this->relation->getAssociated($linkage);
        }
        if ($fields) {
            $result =
                $this->relation->getAssociatedData($linkage, $fields);
        }

        return $result;
    }
}