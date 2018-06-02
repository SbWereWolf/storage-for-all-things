<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 02.06.18 17:16
 */


namespace AllThings\DataObject;


interface ICrossover
{
    function getRightValue(): \string;

    function getLeftValue(): \string;

    function getContent(): \string;

    function setRightValue(\string $value): ICrossover;

    function setLeftValue(\string $value): ICrossover;

    function setContent(\string $value): ICrossover;

    function getCrossoverCopy(): ICrossover;
}
