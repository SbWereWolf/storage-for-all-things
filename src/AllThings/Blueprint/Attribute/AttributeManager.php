<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 17:50
 */

namespace AllThings\Blueprint\Attribute;

use AllThings\DataAccess\Uniquable\UniqueManager;
use AllThings\SearchEngine\Searchable;
use Exception;

class AttributeManager
    extends UniqueManager
    implements IAttributeManager
{
    private ?IAttribute $subject;

    /**
     * @return AttributeRecordHandler
     */
    private function getAttributeHandler(): AttributeRecordHandler
    {
        $handler = new AttributeRecordHandler(
            $this->subject->getCode(),
            $this->storageLocation,
            $this->dataPath,
        );
        $handler->setSubject($this->subject);

        return $handler;
    }

    /**
     * @param bool $isSuccess
     * @param AttributeRecordHandler $handler
     */
    public function loadSubject(bool $isSuccess, AttributeRecordHandler $handler): void
    {
        if ($isSuccess) {
            $this->subject = $handler->retrieve();
        }
    }

    public function correct(string $targetIdentity = ''): bool
    {
        $handler = $this->getAttributeHandler();

        $result = $handler->write($targetIdentity);

        $this->loadSubject($result, $handler);

        return $result;
    }

    public function browse(): bool
    {
        $handler = $this->getAttributeHandler();

        $result = $handler->read();

        $this->loadSubject($result, $handler);

        return $result;
    }

    public function retrieve(): IAttribute
    {
        $data = $this->subject->GetAttributeCopy();

        return $data;
    }

    public function has(): bool
    {
        return !is_null($this->subject);
    }

    public function getLocation(): string
    {
        $dataType = $this->getDataType();

        $isAcceptable = in_array(
            $dataType,
            array_keys(Searchable::DATA_LOCATION),
            true
        );
        if (!$isAcceptable) {
            throw new Exception(
                'Data location'
                . " for `$dataType` is not defined"
            );
        }

        $table = Searchable::DATA_LOCATION[$dataType];

        return $table;
    }

    public function getFormat(): string
    {
        $dataType = $this->getDataType();

        $isAcceptable = in_array(
            $dataType,
            array_keys(Searchable::DATA_FORMAT),
            true
        );
        if (!$isAcceptable) {
            throw new Exception(
                'Format for type'
                . " `$dataType` is not defined"
            );
        }

        $table = Searchable::DATA_FORMAT[$dataType];

        return $table;
    }

    /**
     * @return string
     */
    private function getDataType(): string
    {
        $this->browse();
        $dataType = $this->retrieve()->getDataType();

        return $dataType;
    }

    /**
     * @param IAttribute|null $subject
     */
    public function setSubject(IAttribute $subject): static
    {
        $this->subject = $subject;

        return $this;
    }
}
