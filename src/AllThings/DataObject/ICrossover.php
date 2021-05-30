<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 02.06.18 17:16
 */


namespace AllThings\DataObject;


interface ICrossover
{
    public function getRightValue(): string;

    public function getLeftValue(): string;

    public function getContent(): string;

    public function setRightValue(string $value): ICrossover;

    public function setLeftValue(string $value): ICrossover;

    public function setContent(string $value): ICrossover;

    public function getCrossoverCopy(): ICrossover;
}
