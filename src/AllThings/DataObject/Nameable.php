<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 11:52
 */

namespace AllThings\DataObject;


interface Nameable
{
    function setCode(string $value): Nameable;

    function getCode(): string;

    function setTitle(string $value): Nameable;

    function getTitle(): string;

    function setRemark(string $value): Nameable;

    function getRemark(): string;

    function getNameableCopy(): Nameable;
}
