<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\DataAccess\Linkage;


interface ILinkageTable
{
    public function getTableName(): string;

    public function getLeftColumn(): string;

    public function getRightColumn(): string;

}