<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\DataAccess\Nameable;

use AllThings\DataAccess\Uniquable\UniqueManager;
use Exception;

class NamedManager extends UniqueManager implements DataManager
{

    /**
     * @throws Exception
     */
    public function correct(object $named): bool
    {
        if (!($named instanceof Nameable)) {
            throw new Exception('Arg $named MUST BE `Nameable`');
        }
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $this->getNamedHandler()->write($named);

        return $result;
    }

    /**
     * @throws Exception
     */
    public function browse(string $uniqueness): Nameable
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $this->getNamedHandler()->read($uniqueness);

        return $result;
    }


    /**
     * @return NamedHandler
     * @throws Exception
     */
    private function getNamedHandler(): NamedHandler
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $handler = new NamedHandler(
            $this->db,
            $this->storageLocation,
            $this->dataSource,
            $this->uniqueIndex,
        );

        return $handler;
    }
}
