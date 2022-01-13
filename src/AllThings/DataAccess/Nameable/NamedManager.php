<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 13:52
 */

namespace AllThings\DataAccess\Nameable;

use AllThings\DataAccess\DataTransfer\Haves;
use AllThings\DataAccess\Uniquable\UniqueManager;

class NamedManager
    extends UniqueManager
    implements NameableManager,
               Haves
{
    private ?Nameable $subject;

    /**
     * @return NamedHandler
     */
    private function getNameableHandler(): NamedHandler
    {
        $subject = $this->subject;
        $dataPath = $this->dataPath;
        $handler = new NamedHandler(
            $subject->getCode(),
            $this->storageLocation,
            $dataPath,
        );
        $handler->setNamed($subject);

        return $handler;
    }

    /**
     * @param NamedHandler $handler
     */
    private function loadSubject(NamedHandler $handler): void
    {
        $this->subject = $handler->retrieve();
    }

    public function correct(string $targetIdentity = ''): bool
    {
        $handler = $this->getNameableHandler();

        $result = $handler->write($targetIdentity);

        $this->loadSubject($handler);

        return $result;
    }

    public function browse(): bool
    {
        $handler = $this->getNameableHandler();

        $result = $handler->read();

        $this->loadSubject($handler);

        return $result;
    }

    public function retrieve(): Nameable
    {
        $nameable = $this->subject->getNameableCopy();

        return $nameable;
    }

    public function has(): bool
    {
        return !is_null($this->subject);
    }

    /**
     * @param Nameable $subject
     */
    public function setNamed(Nameable $subject): void
    {
        $this->subject = $subject;
    }
}
