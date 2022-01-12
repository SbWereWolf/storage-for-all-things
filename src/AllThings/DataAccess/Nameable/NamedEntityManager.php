<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 17:50
 */

namespace AllThings\DataAccess\Nameable;


use AllThings\DataAccess\Haves;
use AllThings\DataAccess\Uniquable\UniqueManager;

class NamedEntityManager
    extends UniqueManager
    implements INamedEntityManager,
               Haves
{
    private ?Nameable $subject;

    /**
     * @return NamedRecordHandler
     */
    private function getNameableHandler(): NamedRecordHandler
    {
        $subject = $this->subject;
        $dataPath = $this->dataPath;
        $handler = new NamedRecordHandler(
            $subject->getCode(),
            $this->storageLocation,
            $dataPath,
        );
        $handler->setContainer($subject);

        return $handler;
    }

    /**
     * @param NamedRecordHandler $handler
     */
    private function loadSubject(NamedRecordHandler $handler): void
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
    public function setSubject(Nameable $subject): void
    {
        $this->subject = $subject;
    }
}
