<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 1:23
 */


namespace Environment\Presentation;


interface Jsonable
{

    public function toJson(): string;
}
