<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:20
 */

namespace AllThings\Essence;


use AllThings\DataAccess\Handler\EssenceRecordHandler;

class EssenceManager implements IEssenceManager
{
    private $subject = null;
    private $dataPath = null;


    public function __construct(IEssence $subject, \PDO $dataPath)
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
     * @return EssenceRecordHandler
     */
    private function getHandler(): EssenceRecordHandler
    {
        $subject = $this->subject;
        $dataPath = $this->dataPath;
        $handler = new EssenceRecordHandler($subject, $dataPath);

        return $handler;
    }

    /**
     * @param $handler
     */
    private function setSubject(EssenceRecordHandler $handler): void
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

    function retrieveData(): IEssence
    {
        $nameable = $this->subject->getNameableCopy();
        $storable = $this->subject->getStorableCopy();

        $data = new Essence($nameable, $storable);

        return $data;
    }
}
