<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 1:23
 */


namespace AllThings\Presentation;


interface Jsonable
{

    function toJson(): string;
}
