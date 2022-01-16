<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
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
    public function handleWithDirectReading(): StorageManager
    {
        $this->change(Storable::DIRECT_READING);
        $handler = new DirectReading(
            $this->essence,
            $this->db
        );

        $isSuccess = $handler->setup();
        if (!$isSuccess) {
            throw new Exception(
                'DB source'
                . ' must be created with success'
            );
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function handleWithRapidObtainment(): StorageManager
    {
        $this->change(Storable::RAPID_OBTAINMENT);
        $handler = new RapidObtainment(
            $this->essence,
            $this->db
        );

        $isSuccess = $handler->setup();
        if (!$isSuccess) {
            throw new Exception(
                'DB source'
                . ' must be created with success'
            );
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function handleWithRapidRecording(): StorageManager
    {
        $this->change(Storable::RAPID_RECORDING);
        $handler = new RapidRecording(
            $this->essence,
            $this->db
        );

        $isSuccess = $handler->setup();
        if (!$isSuccess) {
            throw new Exception(
                'DB source'
                . ' must be created with success'
            );
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function change(string $storageManner): bool
    {
        $essence = $this->reload();

        $modified = (new EssenceFactory())
            ->setCode($essence->getCode())
            ->setTitle($essence->getTitle())
            ->setRemark($essence->getRemark())
            ->setStorageManner($storageManner)
            ->makeEssence();
        $handler = new EssenceManager($this->db, 'essence',);

        $isSuccess = $handler->correct($modified);
        if (!$isSuccess) {
            throw new Exception(
                'Essence must be corrected with success'
            );
        }

        /** @noinspection PhpExpressionAlwaysConstantInspection */
        return $isSuccess;
    }

    /**
     * @throws Exception
     */
    public function setup(string $attribute = '', string $dataType = ''): StorageManager
    {
        $dataHandler = $this->getHandler();

        $isSuccess = $dataHandler->setup($attribute, $dataType);
        if (!$isSuccess) {
            throw new Exception(
                'Installation MUST BE defined with success'
            );
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function prune(string $attribute): bool
    {
        $dataHandler = $this->getHandler();

        $isSuccess = $dataHandler->prune($attribute);
        if (!$isSuccess) {
            throw new Exception(
                'Installation MUST BE defined with success'
            );
        }

        /** @noinspection PhpExpressionAlwaysConstantInspection */
        return $isSuccess;
    }

    /**
     * @throws Exception
     */
    public function refresh(array $values = []): StorageManager
    {
        $dataHandler = $this->getHandler();

        $isSuccess = $dataHandler->refresh($values);
        if (!$isSuccess) {
            throw new Exception(
                'Installation MUST BE refreshed with success'
            );
        }

        return $this;
    }

    /**
     * @return Installation
     * @throws Exception
     */
    public function getHandler(): Installation
    {
        $essence = $this->reload();
        if (!$essence) {
            throw new Exception('Essence must be find with success');
        }

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