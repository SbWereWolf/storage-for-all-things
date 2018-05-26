<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 26.05.18 12:21
 */

namespace AllThings\Common;


use AllThings\DataAccess\Handler\NamedRecordHandler;
use AllThings\DataObject\Nameable;

class NamedEntityManager implements INamedEntityManager
{
    private $subject = null;
    private $dataPath = null;

    public function __construct(Nameable $subject, \PDO $dataPath)
    {
        $this->subject = $subject;
        $this->dataPath = $dataPath;
    }

    function create(string $targetIdentity): bool
    {

        $handler = $this->getHandler();

        $result = $handler->insert($targetIdentity);

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
        $handler = new NamedRecordHandler($subject, $dataPath);

        return $handler;
    }

    /**
     * @param $handler
     */
    private function setSubject(NamedRecordHandler $handler): void
    {
        $this->subject = $handler->retrieveData();
    }

    function remove(string $targetIdentity): bool
    {
        $handler = $this->getHandler();

        $result = $handler->hide($targetIdentity);

        $this->setSubject($handler);

        return $result;
    }

    function correct(string $targetIdentity): bool
    {
        $handler = $this->getHandler();

        $result = $handler->write($targetIdentity);

        $this->setSubject($handler);

        return $result;
    }

    function browse(string $targetIdentity): bool
    {
        $handler = $this->getHandler();

        $result = $handler->read($targetIdentity);

        $this->setSubject($handler);

        return $result;
    }

    function retrieveData(): Nameable
    {
        $nameable = $this->subject->getNameableCopy();

        return $nameable;
    }
}
