<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\ControlPanel;

use AllThings\DataAccess\Nameable\Nameable;
use AllThings\DataAccess\Nameable\NamedFactory;
use AllThings\DataAccess\Nameable\NamedManager;
use Exception;
use PDO;

class ProductionLine
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
        $thingManager = new NamedManager(
            $this->db,
            'thing',
        );

        $isSuccess = $thingManager->create($code);
        if (!$isSuccess) {
            throw new Exception(
                'Product must be created with success'
            );
        }

        $named = (new NamedFactory())
            ->setCode($code)
            ->setTitle($title)
            ->setRemark($description)
            ->makeNameable();
        if ($title || $description) {
            $isSuccess = $thingManager->correct($named);
        }
        if (!$isSuccess) {
            throw new Exception(
                'Product must be updated with success'
            );
        }

        return $named;
    }
}