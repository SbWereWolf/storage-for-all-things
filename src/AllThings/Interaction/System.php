<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 13:52
 */

namespace AllThings\Interaction;

use PDO;

class System
{
    private PDO $db;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

}