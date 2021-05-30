<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:20
 */

namespace AllThings\Essence;


use AllThings\DataAccess\Handler\EssenceRecordHandler;
use PDO;

class EssenceManager implements IEssenceManager
{
    private $subject = null;
    private $dataPath = null;


    public function __construct(IEssence $subject, PDO $dataPath)
    {
        $this->subject = $subject;
        $this->dataPath = $dataPath;
    }

    public function create(): bool
    {
        $handler = $this->getHandler();

        $result = $handler->add();

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
    private function setSubject(EssenceRecordHandler $handler)
    {
        $this->subject = $handler->has()
            ? $handler->retrieveData()
            : null;
    }

    public function remove(): bool
    {
        $handler = $this->getHandler();

        $result = $handler->hide();

        $this->setSubject($handler);

        return $result;
    }

    public function correct(string $targetIdentity): bool
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
}
