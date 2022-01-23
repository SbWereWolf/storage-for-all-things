<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 23.01.2022, 12:53
 */

namespace AllThings\DataAccess\Linkage;

interface RelationReading
{
    public function getRelatedFields(
        ILinkage $linkage,
        string $field,
    ): array;

    public function getRelatedRecords(
        ILinkage $linkage,
        array $fields
    ): array;
}
