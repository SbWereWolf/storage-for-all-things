<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 05.01.2022, 2:51
 */

namespace AllThings\Blueprint\Attribute;

use AllThings\DataAccess\Nameable\Valuable;
use AllThings\DataAccess\Retrievable;
use AllThings\DataAccess\Uniquable\UniqueHandler;

class AttributeRecordHandler extends UniqueHandler implements Valuable, Retrievable
{
    private string $dataSource = 'attribute';
    private ?IAttribute $attribute = null;

    private function getAttributeLocation(): AttributeLocation
    {
        $repository = new AttributeLocation($this->location, $this->dataPath);
        return $repository;
    }

    /**
     * @param bool $result
     * @param IAttribute $attribute
     */
    public function assignAttribute(bool $result, IAttribute $attribute): void
    {
        if ($result) {
            $this->attribute = $attribute;
        }
        if (!$result) {
            $this->attribute = null;
        }
    }

    public function write(string $code): bool
    {
        $attribute = $this->attribute->GetAttributeCopy();
        $target = $attribute;
        if ($code) {
            $target = $this->setAttributeByCode($code);
        }

        $result = ($this->getAttributeLocation())->update($target, $attribute);

        $this->assignAttribute($result, $attribute);

        return $result;
    }

    /**
     * @param string $code
     * @return IAttribute
     */
    private function setAttributeByCode(string $code): IAttribute
    {
        $attribute = Attribute::GetDefaultAttribute();
        $attribute->setCode($code);

        return $attribute;
    }

    public function read(): bool
    {
        $result = ($this->getAttributeSource())->select($this->attribute);

        $this->assignAttribute($result, $this->attribute);

        return $result;
    }

    private function getAttributeSource(): AttributeSource
    {
        $repository = new AttributeSource($this->dataSource, $this->dataPath);

        return $repository;
    }

    public function retrieveData(): IAttribute
    {
        $essence = $this->attribute->GetAttributeCopy();

        return $essence;
    }

    public function has(): bool
    {
        return !is_null($this->attribute);
    }

    public function setSubject(?IAttribute $subject)
    {
        $this->attribute = $subject;
    }
}
