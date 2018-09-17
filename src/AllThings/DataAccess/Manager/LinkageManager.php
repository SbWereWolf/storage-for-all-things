<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 3:27
 */

namespace AllThings\DataAccess\Manager;


interface LinkageManager
{

    function setUp(): bool;

    function breakDown(): bool;

    function getAssociated(): bool;
}
