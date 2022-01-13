<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 13:52
 */

namespace AllThings\Blueprint\Essence;

use AllThings\DataAccess\Uniquable\UniqueManager;

class EssenceManager extends UniqueManager implements IEssenceManager
{
    private ?IEssence $subject = null;

    /**
     * @return EssenceHandler
     */
    private function getEssenceHandler(): EssenceHandler
    {
        $handler = new EssenceHandler(
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
            $this->subject = $handler->retrieve();
        }

        return $result;
    }

    public function retrieve(): IEssence
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
    public function setEssence(IEssence $subject): bool
    {
        $this->subject = $subject;

        return true;
    }
}
