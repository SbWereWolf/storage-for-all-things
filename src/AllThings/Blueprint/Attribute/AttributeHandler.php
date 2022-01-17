<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 17.01.2022, 7:56
 */

namespace AllThings\Blueprint\Attribute;

use AllThings\DataAccess\Uniquable\UniqueHandler;
use Exception;
use JetBrains\PhpStorm\Pure;
use PDO;

class AttributeHandler
    extends UniqueHandler
    implements IAttributeHandler
{
    protected PDO $db;
    protected string $storageLocation;
    protected string $dataSourceName = '';
    protected string $uniqueIndex;

    /**
     * @throws Exception
     */
    public function read(string $uniqueness): IAttribute
    {
        $source = $this->getSource($uniqueness);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $source->select();

        return $result;
    }

    /**
     * @throws Exception
     */
    public function write(object $attribute): bool
    {
        /*        if (!($attribute instanceof IAttribute)) {
                    $className = IAttribute::class;
                    throw new Exception(
                        "Arg \$attribute MUST BE `$className`"
                    );
                }*/
        $result = false;
        if ($attribute instanceof IAttribute) {
            $result = $this->getLocation($attribute->getCode())
                ->update($attribute);
        }

        return $result;
    }

    #[Pure]
    private function getSource(string $uniqueness): AttributeSource
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $repository = new AttributeSource(
            $this->db,
            $this->dataSourceName,
            $uniqueness,
            $this->uniqueIndex,
        );

        return $repository;
    }

    #[Pure]
    private function getLocation(string $uniqueness): AttributeLocation
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $repository = new AttributeLocation(
            $this->db,
            $this->storageLocation,
            $uniqueness,
            $this->uniqueIndex,
        );
        return $repository;
    }
}
