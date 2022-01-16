<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\Blueprint\Essence;

use AllThings\DataAccess\Nameable\NamedFactory;
use AllThings\StorageEngine\Storable;
use AllThings\StorageEngine\StorageManner;
use Exception;
use JetBrains\PhpStorm\Pure;

class EssenceFactory extends NamedFactory
{
    private string $storageManner;

    #[Pure]
    public function makeEssence(): IEssence
    {
        $nameable = $this->makeNameable();
        $storable = new StorageManner($this->storageManner);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = new Essence($nameable, $storable);

        return $result;
    }

    /**
     * @throws Exception
     */
    public function setStorageManner(string $manner): static
    {
        $isAcceptable = in_array(
            $manner,
            Storable::AVAILABLE,
            true
        );
        if (!$isAcceptable) {
            throw new Exception(
                'Storage manner'
                . ' MUST be one of :'
                . ' view | materialized view | table'
                . ", `$manner` given"
            );
        }
        $this->storageManner = $manner;

        return $this;
    }
}