<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\Blueprint\Essence;

use AllThings\DataAccess\Uniquable\UniqueManager;
use Exception;

class EssenceManager extends UniqueManager implements IEssenceManager
{
    /**
     * @return IEssenceHandler
     * @throws Exception
     */
    private function getEssenceHandler(): IEssenceHandler
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $handler = new EssenceHandler(
            $this->db,
            $this->storageLocation,
            $this->dataSource,
            $this->uniqueIndex,
        );

        return $handler;
    }

    /**
     * @throws Exception
     */
    public function correct(object $attribute): bool
    {
        $handler = $this->getEssenceHandler();
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $handler->write($attribute);

        return $result;
    }

    /**
     * @param string $uniqueness
     *
     * @return IEssence
     * @throws Exception
     */
    public function browse(string $uniqueness): IEssence
    {
        $handler = $this->getEssenceHandler();
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $handler->read($uniqueness);


        return $result;
    }
}
