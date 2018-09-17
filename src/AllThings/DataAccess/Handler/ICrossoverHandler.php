<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 02.06.18 21:47
 */

namespace AllThings\DataAccess\Handler;


use AllThings\DataObject\ICrossover;

interface ICrossoverHandler
{
    function crossing(): \bool;

    function setCrossover(ICrossover $crossover): \bool;

    function getCrossover(ICrossover $crossover): \bool;
}
