<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Linkage;

interface RelationReading
{
    public function getRelatedFields(
        ILinkage $linkage,
        string $filed,
    ): array;

    public function getRelatedRecords(
        ILinkage $linkage,
        array $fields
    ): array;
}
