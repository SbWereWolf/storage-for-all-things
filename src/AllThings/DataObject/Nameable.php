<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 11:52
 */

namespace AllThings\DataObject;


interface Nameable
{
    function setCode (string $code):Nameable;
    function getCode ():string;
    function setTitle (string $title): Nameable;
    function getTitle ():string;
    function setRemark (string $remark): Nameable;
    function getRemark ():string;
    function getNameableCopy():Nameable;
}
