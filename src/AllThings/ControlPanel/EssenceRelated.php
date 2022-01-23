<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 23.01.2022, 12:53
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
     * @param string $related
     * @return bool
     */
    public function attach(string $related): bool
    {
        $linkage = (new Linkage())
            ->setLeftValue($this->essence)
            ->setRightValue($related);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $isSuccess = $this->relation->attach($linkage);
        /*        if (!$isSuccess) {
                    throw new Exception(
                        "Related `$related` must be attached with success"
                    );
                }*/

        return $isSuccess;
    }

    /**
     * @param string $related
     * @return bool
     */
    public function detach(string $related): bool
    {
        $linkage = (new Linkage())
            ->setLeftValue($this->essence)
            ->setRightValue($related);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $isSuccess = $this->relation->detach($linkage);
        /*        if (!$isSuccess) {
                    throw new Exception(
                        "Related `$related` must be detached with success"
                    );
                }*/

        return $isSuccess;
    }

    /**
     * @throws Exception
     */
    public function purge(): bool
    {
        $linkage = (new Linkage())
            ->setLeftValue($this->essence);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $isSuccess = $this->relation->detach($linkage);
        /*        if (!$isSuccess) {
                    throw new Exception(
                        'All Related must be detached with success'
                    );
                }*/

        return $isSuccess;
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