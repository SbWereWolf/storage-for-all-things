<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 11:52
 */

namespace AllThings\DataObject;


interface Nameable
{
    public function setCode(string $value): Nameable;

    public function getCode(): string;

    public function setTitle(string $value): Nameable;

    public function getTitle(): string;

    public function setRemark(string $value): Nameable;

    public function getRemark(): string;

    public function getNameableCopy(): Nameable;
}
