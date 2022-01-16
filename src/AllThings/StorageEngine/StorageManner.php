<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\StorageEngine;


use Exception;
use JetBrains\PhpStorm\Pure;

class StorageManner implements Storable
{
    private string $storageManner;

    public function __construct(string $storageManner = self::UNDEFINED)
    {
        $this->storageManner = $storageManner;
    }

    public function getStorageManner(): string
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $storeAt = $this->storageManner;

        return $storeAt;
    }

    /**
     * @throws Exception
     */
    #[Pure]
    public function getStorableCopy(): Storable
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $copy = (new StorageManner($this->getStorageManner()));

        return $copy;
    }
}
