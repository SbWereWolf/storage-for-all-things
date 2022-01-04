<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 04.01.2022, 10:06
 */

namespace Environment\Database;


interface EnvReading
{
    public function read(): array;
}
