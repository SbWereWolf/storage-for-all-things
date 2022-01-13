<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 13:52
 */

namespace AllThings\StorageEngine;

use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\Blueprint\Essence\Essence;
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
    public function change(string $storageKind): bool
    {
        $essence = $this->reload();
        if (!$essence) {
            throw new Exception('Essence must be find with success');
        }

        $essence->setStorageKind($storageKind);
        $handler = new EssenceManager(
            $essence->getCode(),
            'essence',
            $this->db
        );
        $handler->setEssence($essence);

        $isSuccess = $handler->correct();
        if (!$isSuccess) {
            throw new Exception(
                'Essence must be corrected with success'
            );
        }

        return $isSuccess;
    }

    public function setup(?IAttribute $attribute = null): StorageManager
    {
        $dataHandler = $this->getHandler();

        $isSuccess = $dataHandler->setup($attribute);
        if (!$isSuccess) {
            throw new Exception(
                'Installation MUST BE defined with success'
            );
        }

        return $this;
    }

    public function prune(string $attribute): bool
    {
        $dataHandler = $this->getHandler();

        $isSuccess = $dataHandler->prune($attribute);
        if (!$isSuccess) {
            throw new Exception(
                'Installation MUST BE defined with success'
            );
        }

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

        $storageKind = $essence->getStorageKind();
        switch ($storageKind) {
            case Storable::DIRECT_READING:
                $source = new DirectReading($this->essence, $this->db);
                break;
            case Storable::RAPID_OBTAINMENT:
                $source = new RapidObtainment($this->essence, $this->db);
                break;
            case Storable::RAPID_RECORDING:
                $source = new RapidRecording($this->essence, $this->db);
                break;
            default:
                throw new Exception(
                    'Storage kind'
                    . ' MUST be one of :'
                    . ' view | materialized view | table'
                    . ", `$storageKind` given"
                );
        }
        return $source;
    }

    private function reload(): ?IEssence
    {
        $essence = (Essence::GetDefaultEssence());
        $essence->setCode($this->essence);

        $manager = new EssenceManager(
            $this->essence,
            'essence',
            $this->db,
        );
        $manager->setEssence($essence);

        $isSuccess = $manager->browse();
        $result = null;
        if ($isSuccess && $manager->has()) {
            $result = $manager->retrieve();
        }

        return $result;
    }
}