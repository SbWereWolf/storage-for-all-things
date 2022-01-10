<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\DataAccess\Linkage;

interface ILinkage
{
    public function getRightValue(): string;

    public function getLeftValue(): string;

    public function setRightValue(string $value): ILinkage;

    public function setLeftValue(string $value): ILinkage;

    public function getLinkageCopy(): ILinkage;
}
