<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 11:52
 */

namespace AllThings\DataObject;


interface Named
{
    function setCode (string $code):Named;
    function getCode ():string;
    function setTitle (string $title): Named;
    function getTitle ():string;
    function setRemark (string $remark): Named;
    function getRemark ():string;
    function getDuplicate():Named;
}
