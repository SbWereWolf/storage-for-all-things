<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */

namespace AllThings\Blueprint\Attribute;


use PDO;

class AttributeManager implements IAttributeManager
{
    private $subject = null;
    private $dataPath = null;


    public function __construct(IAttribute $subject, PDO $dataPath)
    {
        $this->subject = $subject;
        $this->dataPath = $dataPath;
    }

    public function create(): bool
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
    private function setSubject(bool $isSuccess, AttributeRecordHandler $handler): void
    {
        if ($isSuccess) {
            $this->subject = $handler->retrieveData();
        }
    }

    public function remove(): bool
    {
        $handler = $this->getHandler();

        $result = $handler->hide();

        $this->setSubject($result, $handler);

        return $result;
    }

    public function correct(string $targetIdentity = ''): bool
    {
        $handler = $this->getHandler();

        $result = $handler->write($targetIdentity);

        $this->setSubject($result, $handler);

        return $result;
    }

    public function browse(): bool
    {
        $handler = $this->getHandler();

        $result = $handler->read();

        $this->setSubject($result, $handler);

        return $result;
    }

    public function retrieveData(): IAttribute
    {
        $data = $this->subject->GetAttributeCopy();

        return $data;
    }

    public function has(): bool
    {
        return !is_null($this->subject);
    }
}
