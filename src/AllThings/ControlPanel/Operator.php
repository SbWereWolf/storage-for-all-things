<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 03.07.2021, 10:08
 */

namespace AllThings\ControlPanel;


use AllThings\Blueprint\Essence\Essence;
use AllThings\Blueprint\Essence\EssenceManager;
use AllThings\Blueprint\Essence\IEssence;
use AllThings\StorageEngine\Storable;
use Exception;
use PDO;

class Operator
{
    private PDO $db;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    public function createBlueprint(
        string $code,
        string $storageKind = Storable::DIRECT_READING,
        string $title = '',
        string $description = ''
    ): IEssence {
        $essence = Essence::GetDefaultEssence();
        $essence->setCode($code);

        $handler = new EssenceManager(
            $essence,
            $this->db
        );
        $isSuccess = $handler->create();
        if (!$isSuccess) {
            throw new Exception('Essence must be created with success');
        }

        if ($storageKind) {
            $essence->setStorage($storageKind);
        }
        if ($title) {
            $essence->setTitle($title);
        }
        if ($description) {
            $essence->setRemark($description);
        }
        if ($storageKind || $title || $description) {
            $handler = new EssenceManager(
                $essence,
                $this->db
            );
            $isSuccess = $handler->correct($code);
        }
        if (!$isSuccess) {
            throw new Exception('Essence must be updated with success');
        }

        return $essence;
    }

    public function changeStorage(
        IEssence $essence,
        string $storageKind
    ) {
    }
}