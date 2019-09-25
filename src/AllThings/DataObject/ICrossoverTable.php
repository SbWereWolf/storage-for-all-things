<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 02.06.18 20:22
 */

namespace AllThings\DataObject;


interface ICrossoverTable
{
    function getTableName(): string;

    function getLeftColumn(): string;

    function getRightColumn(): string;

}
