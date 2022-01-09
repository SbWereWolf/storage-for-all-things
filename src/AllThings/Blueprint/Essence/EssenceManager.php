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
        $data = $this->subject->GetEssenceCopy();
        $handler->setEssence($data);

        return $handler;
    }

    public function correct(string $targetIdentity = ''): bool
    {
        $handler = $this->getEssenceHandler();

        $result = $handler->write($targetIdentity);

        return $result;
    }

    public function browse(): bool
    {
        $handler = $this->getEssenceHandler();

        $result = $handler->read();

        if ($result) {
            $this->subject = $handler->retrieveData();
        }

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
    public function setSubject(IEssence $subject): bool
    {
        $this->subject = $subject;

        return true;
    }
}
