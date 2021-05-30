<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 3:27
 */

namespace AllThings\DataAccess\Manager;


interface LinkageManager
{

    public function setUp(): bool;

    public function breakDown(): bool;

    public function getAssociated(): bool;
}
