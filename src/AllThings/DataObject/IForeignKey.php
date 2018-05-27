<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 10:41
 */


namespace AllThings\DataObject;


interface IForeignKey
{
    function setTable(\string $value): IForeignKey;

    function getTable(): \string;

    function setColumn(\string $value): IForeignKey;

    function getColumn(): \string;

    function setIndex(\string $value): IForeignKey;

    function getIndex(): \string;

}
