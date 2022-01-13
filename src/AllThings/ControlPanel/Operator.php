<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 3:02
 */

namespace AllThings\ControlPanel;

use AllThings\DataAccess\Nameable\Nameable;
use AllThings\DataAccess\Nameable\NamedEntity;
use AllThings\DataAccess\Nameable\NamedManager;
use Exception;
use PDO;

class Operator
{
    private PDO $db;

    /**
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    /**
     * @param string $code
     * @param string $title
     * @param string $description
     *
     * @return Nameable
     * @throws Exception
     */
    public function create(
        string $code,
        string $title = '',
        string $description = '',
    ): Nameable {
        $nameable = (new NamedEntity())->setCode($code);
        $thingManager = new NamedManager(
            $code,
            'thing',
            $this->db
        );

        $isSuccess = $thingManager->create();
        if (!$isSuccess) {
            throw new Exception(
                'Product must be created with success'
            );
        }

        if ($title) {
            $nameable->setTitle($title);
        }
        if ($description) {
            $nameable->setRemark($description);
        }
        $thingManager->setNamed($nameable);
        if ($title || $description) {
            $isSuccess = $thingManager->correct();
        }
        if (!$isSuccess) {
            throw new Exception(
                'Product must be updated with success'
            );
        }

        return $nameable;
    }
}