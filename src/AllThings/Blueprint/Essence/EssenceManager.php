<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\Blueprint\Essence;

use AllThings\DataAccess\Uniquable\UniqueManager;

class EssenceManager extends UniqueManager implements IEssenceManager
{
    private ?IEssence $subject = null;

    /**
     * @return EssenceRecordHandler
     */
    private function getEssenceHandler(): EssenceRecordHandler
    {
        $handler = new EssenceRecordHandler(
            $this->subject->getCode(),
            $this->storageLocation,
            $this->dataPath,
        );
        $handler->setEssence($this->subject);

        return $handler;
    }

    private function loadSubject(EssenceRecordHandler $handler)
    {
        $this->subject = $handler->has()
            ? $handler->retrieveData()
            : null;
    }

    public function correct(string $targetIdentity = ''): bool
    {
        $handler = $this->getEssenceHandler();

        $result = $handler->write($targetIdentity);

        $this->loadSubject($handler);

        return $result;
    }

    public function browse(): bool
    {
        $handler = $this->getEssenceHandler();

        $result = $handler->read();

        $this->loadSubject($handler);

        return $result;
    }

    public function retrieveData(): IEssence
    {
        $nameable = $this->subject->getNameableCopy();
        $storable = $this->subject->getStorableCopy();

        $data = new Essence($nameable, $storable);

        return $data;
    }

    public function has(): bool
    {
        return !is_null($this->subject);
    }

    /**
     * @param IEssence|null $subject
     */
    public function setSubject(IEssence $subject): void
    {
        $this->subject = $subject;
    }
}
