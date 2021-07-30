<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */

namespace AllThings\DataAccess\Nameable;


use PDO;

class NamedEntityManager implements INamedEntityManager
{
    private $subject = null;
    private $dataPath = null;
    private $storageLocation = '';

    public function __construct(Nameable $subject, $storageLocation, PDO $dataPath)
    {
        $this->subject = $subject;
        $this->dataPath = $dataPath;
        $this->storageLocation = $storageLocation;
    }

    public function create(): bool
    {
        $handler = $this->getHandler();

        $result = $handler->add();

        $this->setSubject($handler);

        return $result;
    }

    /**
     * @return NamedRecordHandler
     */
    private function getHandler(): NamedRecordHandler
    {
        $subject = $this->subject;
        $dataPath = $this->dataPath;
        $handler = new NamedRecordHandler($subject, $this->storageLocation, $dataPath);

        return $handler;
    }

    /**
     * @param $handler
     */
    private function setSubject(NamedRecordHandler $handler): void
    {
        $this->subject = $handler->retrieveData();
    }

    public function remove(): bool
    {
        $handler = $this->getHandler();

        $result = $handler->hide();

        $this->setSubject($handler);

        return $result;
    }

    public function correct(string $targetIdentity = ''): bool
    {
        $handler = $this->getHandler();

        $result = $handler->write($targetIdentity);

        $this->setSubject($handler);

        return $result;
    }

    public function browse(): bool
    {
        $handler = $this->getHandler();

        $result = $handler->read();

        $this->setSubject($handler);

        return $result;
    }

    public function retrieveData(): Nameable
    {
        $nameable = $this->subject->getNameableCopy();

        return $nameable;
    }

    public function has(): bool
    {
        return !is_null($this->subject);
    }
}
