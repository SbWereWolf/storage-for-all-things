<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 03.07.2021, 17:12
 */

namespace AllThings\ControlPanel;


use AllThings\Blueprint\Essence\EssenceManager;
use AllThings\Blueprint\Essence\IEssence;
use PDO;

class Editor
{
    private PDO $db;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    public function changeStorage(
        IEssence $essence,
        string $storageKind
    ) {
        $essence->setStorage($storageKind);
        $handler = new EssenceManager(
            $essence,
            $this->db
        );
        /** @noinspection PhpUnusedLocalVariableInspection */
        $isSuccess = $handler->correct();

        return $this;
    }
}