<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 10:41
 */


namespace AllThings\DataObject;


interface IForeignKey
{
    public function getTable(): string;

    public function getColumn(): string;

    public function getIndex(): string;

}
