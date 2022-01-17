<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 17.01.2022, 7:56
 */

namespace AllThings\Blueprint\Essence;

use AllThings\DataAccess\Uniquable\UniqueHandler;
use Exception;
use JetBrains\PhpStorm\Pure;

class EssenceHandler
    extends UniqueHandler
    implements IEssenceHandler
{
    /**
     * @throws Exception
     */
    public function read(string $uniqueness): IEssence
    {
        $source = $this->getSource($uniqueness);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $source->select();

        return $result;
    }

    /**
     * @param object $essence
     *
     * @return bool
     * @throws Exception
     */
    public function write(object $essence): bool
    {
        /*        if (!($essence instanceof IEssence)) {
                    $className = IEssence::class;
                    throw new Exception("Arg \$named MUST BE `$className`");
                }*/
        $result = false;
        if ($essence instanceof IEssence) {
            $result = $this->getLocation($essence->getCode())
                ->update($essence);
        }


        return $result;
    }

    #[Pure]
    private function getLocation(string $uniqueness): EssenceLocation
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $repository = new EssenceLocation(
            $this->db,
            $this->storageLocation,
            $uniqueness,
            $this->uniqueIndex,
        );

        return $repository;
    }

    #[Pure]
    private function getSource(string $uniqueness): EssenceSource
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $repository = new EssenceSource(
            $this->db,
            $this->dataSourceName,
            $uniqueness,
            $this->uniqueIndex,
        );

        return $repository;
    }
}
