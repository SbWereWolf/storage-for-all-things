<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 23.01.2022, 12:53
 */

namespace AllThings\DataAccess\Linkage;

interface ILinkageManager
{
    public function attach(ILinkage $linkage): bool;

    public function detach(ILinkage $linkage): bool;

    /**
     * @param ILinkage $linkage
     * @param array $exclude
     * @param string $field
     *
     * @return array
     */
    public function getAssociated(
        ILinkage $linkage,
        string $field,
    ): array;

    public function getAssociatedData(
        ILinkage $linkage,
        array $fields
    ): array;
}
