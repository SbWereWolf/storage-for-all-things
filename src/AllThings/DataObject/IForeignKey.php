<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 10:41
 */


namespace AllThings\DataObject;


interface IForeignKey
{
    function getTable(): string;

    function getColumn(): string;

    function getIndex(): string;

}
