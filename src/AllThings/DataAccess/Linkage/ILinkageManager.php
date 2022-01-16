<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Linkage;

interface ILinkageManager
{
    public function attach(ILinkage $linkage): bool;

    public function detach(ILinkage $linkage): bool;

    /**
     * @param ILinkage $linkage
     * @param string   $filed
     *
     * @return array
     */
    public function getAssociated(
        ILinkage $linkage,
        string $filed,
    ): array;

    public function getAssociatedData(
        ILinkage $linkage,
        array $fields
    ): array;
}
