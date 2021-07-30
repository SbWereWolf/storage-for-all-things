<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:39
 */

namespace AllThings\Blueprint\Attribute;


use AllThings\DataAccess\Nameable\Hideable;
use AllThings\DataAccess\Nameable\Valuable;
use AllThings\DataAccess\Retrievable;
use PDO;

class AttributeRecordHandler implements Valuable, Hideable, Retrievable
{

    private $storageLocation = 'attribute';
    private $dataSource = 'attribute';

    private $dataPath = null;
    private $attribute = null;

    public function __construct(IAttribute $attribute, PDO $dataPath)
    {
        $this->attribute = $attribute;
        $this->dataPath = $dataPath;
    }

    public function add(): bool
    {
        $attribute = $this->attribute->GetAttributeCopy();

        $result = ($this->getAttributeLocation())->insert($attribute);

        $this->setAttribute($result, $attribute);

        return $result;
    }

    private function getAttributeLocation(): AttributeLocation
    {
        $repository = new AttributeLocation($this->storageLocation, $this->dataPath);
        return $repository;
    }

    /**
     * @param $result
     * @param $attribute
     */
    private function setAttribute(bool $result, IAttribute $attribute): void
    {
        if ($result) {
            $this->attribute = $attribute;
        }
        if (!$result) {
            $this->attribute = null;
        }
    }

    public function hide(): bool
    {
        $attribute = $this->attribute->GetAttributeCopy();

        $result = ($this->getAttributeLocation())->setIsHidden($attribute);

        $this->setAttribute($result, $attribute);

        return $result;
    }

    public function write(string $code): bool
    {
        $attribute = $this->attribute->GetAttributeCopy();
        $target = $attribute;
        if ($code) {
            $target = $this->setAttributeByCode($code);
        }

        $result = ($this->getAttributeLocation())->update($target, $attribute);

        $this->setAttribute($result, $attribute);

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

        $this->setAttribute($result, $this->attribute);

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
}
