<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 13:33
 */

namespace AllThings\ControlPanel;

use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\Blueprint\Essence\Essence;
use AllThings\Blueprint\Essence\EssenceManager;
use AllThings\Blueprint\Essence\IEssence;
use AllThings\StorageEngine\DirectReading;
use AllThings\StorageEngine\Installation;
use AllThings\StorageEngine\RapidObtainment;
use AllThings\StorageEngine\RapidRecording;
use AllThings\StorageEngine\Storable;
use Exception;
use PDO;

class Lots
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
    public function handleWithDirectReading(): Lots
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
    public function handleWithRapidObtainment(): Lots
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
    public function handleWithRapidRecording(): Lots
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

    public function create(
        string $title = '',
        string $description = '',
        string $storageKind = Storable::DIRECT_READING
    ): IEssence {
        $essence = Essence::GetDefaultEssence();
        $code = $this->essence;
        $essence->setCode($code);

        $handler = new EssenceManager(
            $code,
            'essence',
            $this->db,
        );
        $isSuccess = $handler->create();
        if (!$isSuccess) {
            throw new Exception('Essence must be created with success');
        }

        if ($storageKind) {
            $essence->setStorageKind($storageKind);
        }
        if ($title) {
            $essence->setTitle($title);
        }
        if ($description) {
            $essence->setRemark($description);
        }
        $handler->setSubject($essence);

        if ($storageKind || $title || $description) {
            $isSuccess = $handler->correct();
        }
        if (!$isSuccess) {
            throw new Exception('Essence must be updated with success');
        }

        return $essence;
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
        $handler->setSubject($essence);

        $isSuccess = $handler->correct();
        if (!$isSuccess) {
            throw new Exception(
                'Essence must be corrected with success'
            );
        }

        return $isSuccess;
    }

    public function setup(?IAttribute $attribute = null): Lots
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
    public function refresh(array $values = []): Lots
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
        $manager->setSubject($essence);

        $isSuccess = $manager->browse();
        $result = null;
        if ($isSuccess && $manager->has()) {
            $result = $manager->retrieveData();
        }

        return $result;
    }
}