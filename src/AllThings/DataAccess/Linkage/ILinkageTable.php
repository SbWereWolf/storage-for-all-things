<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 6:19
 */

namespace AllThings\DataAccess\Linkage;


interface ILinkageTable
{
    public function getTableName(): string;

    public function getLeftColumn(): string;

    public function getRightColumn(): string;

    public function getLeftKey(): IForeignKey;

    public function getRightKey(): IForeignKey;

}
