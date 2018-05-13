<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 23:19
 */

namespace AllThings\DataObject;


interface Crossover
{
    function getPrimary():string;
    function setPrimary():Crossover;
    function getSecondary():string;
    function setSecondary():Crossover;
    function getContent():string;
    function setContent():Crossover;
    function getDuplicate():Crossover;

}
