<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 31.12.2021, 13:37
 */

namespace AllThings\ControlPanel;


use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\Blueprint\Essence\Essence;
use AllThings\Blueprint\Essence\EssenceManager;
use AllThings\Blueprint\Essence\IEssence;
use AllThings\DataAccess\Crossover\ICrossover;
use AllThings\StorageEngine\DirectReading;
use AllThings\StorageEngine\Installation;
use AllThings\StorageEngine\RapidObtainment;
use AllThings\StorageEngine\RapidRecording;
use AllThings\StorageEngine\Storable;
use Exception;
use PDO;

class Schema
{
    private PDO $db;
    private string $essence;

    public function __construct(PDO $connection, string $essence)
    {
        $this->db = $connection;
        $this->essence = $essence;
    }

    /**
     * @throws Exception
     */
    public function refresh(?ICrossover $value = null): Schema
    {
        $installation = $this->getInstallation();

        $isSuccess = $installation->refresh($value);
        if (!$isSuccess) {
            throw new Exception('Installation MUST BE refreshed with success');
        }

        return $this;
    }

    /**
     * @return Installation
     * @throws Exception
     */
    public function getInstallation(): Installation
    {
        $payload = $this->readEssence();
        if (count($payload) === 0) {
            throw new Exception('Essence must be find with success');
        }
        $essence = $payload[0];

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
                throw new Exception('Storage kind'
                    . ' MUST be one of :'
                    . ' view | materialized view | table'
                    . ", `$storageKind` given");
        }
        return $source;
    }

    /**
     * @return IEssence[]
     * @throws Exception
     */
    private function readEssence(): array
    {
        $essence = (Essence::GetDefaultEssence());
        $essence->setCode($this->essence);
        $manager = new EssenceManager(
            $essence,
            $this->db
        );

        $isSuccess = $manager->browse();
        $data = [];
        if ($isSuccess && $manager->has()) {
            $essence = $manager->retrieveData();
            $data[] = $essence;
        }

        return $data;
    }

    public function setup(?IAttribute $attribute = null): Schema
    {
        $installation = $this->getInstallation();

        $isSuccess = $installation->setup($attribute);
        if (!$isSuccess) {
            throw new Exception('Installation MUST BE defined with success');
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function handleWithDirectReading(): Schema
    {
        $this->changeStorage(Storable::DIRECT_READING);
        $handler = new DirectReading(
            $this->essence,
            $this->db
        );

        $isSuccess = $handler->setup();
        if (!$isSuccess) {
            throw new Exception('DB source'
                . ' must be created with success');
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function changeStorage(string $storageKind): Schema
    {
        $payload = $this->readEssence();
        if (count($payload) === 0) {
            throw new Exception('Essence must be find with success');
        }
        $essence = $payload[0];
        $essence->setStorageKind($storageKind);
        $handler = new EssenceManager(
            $essence,
            $this->db
        );
        /** @noinspection PhpUnusedLocalVariableInspection */
        $isSuccess = $handler->correct();

        return $this;
    }

    /**
     * @throws Exception
     */
    public function handleWithRapidObtainment(): Schema
    {
        $this->changeStorage(Storable::RAPID_OBTAINMENT);
        $handler = new RapidObtainment(
            $this->essence,
            $this->db
        );

        $isSuccess = $handler->setup();
        if (!$isSuccess) {
            throw new Exception('DB source'
                . ' must be created with success');
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function handleWithRapidRecording(): Schema
    {
        $this->changeStorage(Storable::RAPID_RECORDING);
        $handler = new RapidRecording(
            $this->essence,
            $this->db
        );

        $isSuccess = $handler->setup();
        if (!$isSuccess) {
            throw new Exception('DB source'
                . ' must be created with success');
        }

        return $this;
    }
}