<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 17.01.2022, 7:56
 */

namespace AllThings\StorageEngine;

use AllThings\Blueprint\Essence\EssenceFactory;
use AllThings\Blueprint\Essence\EssenceManager;
use AllThings\Blueprint\Essence\IEssence;
use Exception;
use PDO;

class StorageManager
{
    private PDO $db;
    private string $essence;

    /**
     * @param PDO    $connection
     * @param string $essence
     */
    public function __construct(PDO $connection, string $essence)
    {
        $this->db = $connection;
        $this->essence = $essence;
    }

    /**
     * @throws Exception
     */
    public function handleWithDirectReading(): bool
    {
        $this->change(Storable::DIRECT_READING);
        $handler = new DirectReading(
            $this->essence,
            $this->db
        );

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $isSuccess = $handler->setup();
        /*        if (!$isSuccess) {
                    throw new Exception(
                        'DB source'
                        . ' must be created with success'
                    );
                }*/

        return $isSuccess;
    }

    /**
     * @throws Exception
     */
    public function handleWithRapidObtainment(): bool
    {
        $this->change(Storable::RAPID_OBTAINMENT);
        $handler = new RapidObtainment(
            $this->essence,
            $this->db
        );

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $isSuccess = $handler->setup();
        /*        if (!$isSuccess) {
                    throw new Exception(
                        'DB source'
                        . ' must be created with success'
                    );
                }*/

        return $isSuccess;
    }

    /**
     * @throws Exception
     */
    public function handleWithRapidRecording(): bool
    {
        $this->change(Storable::RAPID_RECORDING);
        $handler = new RapidRecording(
            $this->essence,
            $this->db
        );

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $isSuccess = $handler->setup();
        /*        if (!$isSuccess) {
                    throw new Exception(
                        'DB source'
                        . ' must be created with success'
                    );
                }*/

        return $isSuccess;
    }

    /**
     * @throws Exception
     */
    public function change(string $storageManner): bool
    {
        $essence = $this->reload();

        $modified = (new EssenceFactory())
            ->setNameable($essence)
            ->setStorageManner($storageManner)
            ->makeEssence();
        $handler = new EssenceManager($this->db, 'essence',);

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $isSuccess = $handler->correct($modified);
        /*        if (!$isSuccess) {
                    throw new Exception(
                        'Essence must be corrected with success'
                    );
                }*/

        return $isSuccess;
    }

    /**
     * @throws Exception
     */
    public function setup(string $attribute = '', string $dataType = ''): bool
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $isSuccess = $this->getHandler()->setup($attribute, $dataType);
        /*        if (!$isSuccess) {
                    throw new Exception(
                        'Installation MUST BE defined with success'
                    );
                }*/

        return $isSuccess;
    }

    /**
     * @throws Exception
     */
    public function prune(string $attribute): bool
    {
        $dataHandler = $this->getHandler();

        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $isSuccess = $dataHandler->prune($attribute);
        /*        if (!$isSuccess) {
                    throw new Exception(
                        'Installation MUST BE pruned with success'
                    );
                }*/

        return $isSuccess;
    }

    /**
     * @throws Exception
     */
    public function refresh(array $values = []): bool
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $isSuccess = $this->getHandler()->refresh($values);
        /*        if (!$isSuccess) {
                    throw new Exception(
                        'Installation MUST BE refreshed with success'
                    );
                }*/

        return $isSuccess;
    }


    /**
     * @throws Exception
     */
    public function drop(): bool
    {
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $isSuccess = $this->getHandler()->drop();

        return $isSuccess;
    }

    /**
     * @return Installation
     * @throws Exception
     */
    public function getHandler(): Installation
    {
        $essence = $this->reload();
        $storageKind = $essence->getStorageManner();
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $source = match ($storageKind) {
            Storable::DIRECT_READING =>
            new DirectReading($this->essence, $this->db),
            Storable::RAPID_OBTAINMENT =>
            new RapidObtainment($this->essence, $this->db),
            Storable::RAPID_RECORDING =>
            new RapidRecording($this->essence, $this->db),
            default => throw new Exception(
                'Storage kind'
                . ' MUST be one of :'
                . ' view | materialized view | table'
                . ", `$storageKind` given"
            ),
        };

        return $source;
    }

    /**
     * @return IEssence
     * @throws Exception
     */
    private function reload(): IEssence
    {
        $manager = new EssenceManager($this->db, 'essence',);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $manager->browse($this->essence);

        return $result;
    }
}