<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Linkage;


interface ILinkageTable
{
    public function getTableName(): string;

    public function getLeftForeign(): string;

    public function getRightForeign(): string;

    public function getLeftKey(): IForeignKey;

    public function getRightKey(): IForeignKey;

}
