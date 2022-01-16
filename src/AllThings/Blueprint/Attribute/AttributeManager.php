<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\Blueprint\Attribute;

use AllThings\DataAccess\Uniquable\UniqueManager;
use Exception;

class AttributeManager
    extends UniqueManager
    implements IAttributeManager
{

    /**
     * @throws Exception
     */
    private function getAttributeHandler(): IAttributeHandler
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $handler = new AttributeHandler(
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
        $handler = $this->getAttributeHandler();
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $handler->write($attribute);

        return $result;
    }

    /**
     * @throws Exception
     */
    public function browse(string $uniqueness): IAttribute
    {
        $handler = $this->getAttributeHandler();
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $handler->read($uniqueness);

        return $result;
    }
}
