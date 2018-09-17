<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 0:57
 */

namespace AllThings\Essence;


use AllThings\DataAccess\Handler\AttributeRecordHandler;

class AttributeManager implements IAttributeManager
{
    private $subject = null;
    private $dataPath = null;


    public function __construct(IAttribute $subject, \PDO $dataPath)
    {
        $this->subject = $subject;
        $this->dataPath = $dataPath;
    }

    function create(): bool
    {

        $handler = $this->getHandler();

        $result = $handler->add();

        $this->setSubject($result, $handler);

        return $result;
    }

    /**
     * @return AttributeRecordHandler
     */
    private function getHandler(): AttributeRecordHandler
    {
        $subject = $this->subject;
        $dataPath = $this->dataPath;
        $handler = new AttributeRecordHandler($subject, $dataPath);

        return $handler;
    }

    /**
     * @param bool $isSuccess
     * @param AttributeRecordHandler $handler
     */
    private function setSubject(\bool $isSuccess, AttributeRecordHandler $handler): void
    {
        if ($isSuccess) {
            $this->subject = $handler->retrieveData();
        }

    }

    function remove(): bool
    {
        $handler = $this->getHandler();

        $result = $handler->hide();

        $this->setSubject($result, $handler);

        return $result;
    }

    function correct(string $targetIdentity): bool
    {
        $handler = $this->getHandler();

        $result = $handler->write($targetIdentity);

        $this->setSubject($result, $handler);

        return $result;
    }

    function browse(string $targetIdentity): bool
    {
        $handler = $this->getHandler();

        $result = $handler->read($targetIdentity);

        $this->setSubject($result, $handler);

        return $result;
    }

    function retrieveData(): IAttribute
    {
        $data = $this->subject->GetAttributeCopy();

        return $data;
    }
}
