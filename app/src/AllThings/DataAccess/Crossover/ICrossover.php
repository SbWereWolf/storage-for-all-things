<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:40
 */


namespace AllThings\DataAccess\Crossover;


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
