<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 13.05.2018 Time: 20:27
 */

namespace AllThings\DataAccess\Handler;


use AllThings\DataAccess\Implementation\AttributeLocation;
use AllThings\DataAccess\Implementation\AttributeSource;
use AllThings\Essence\Attribute;
use AllThings\Essence\IAttribute;

class AttributeRecordHandler implements Valuable, Hideable, Retrievable
{

    private $storageLocation = 'attribute';
    private $dataSource = 'attribute';

    private $dataPath = null;
    private $attribute = null;

    function __construct(IAttribute $essence, \PDO $dataPath)
    {
        $this->attribute = $essence;
        $this->dataPath = $dataPath;
    }

    function insert(string $code): bool
    {
        $essence = $this->setAttributeByCode($code);

        $result = ($this->getAttributeLocation())->add($essence);

        $this->setAttribute($result, $essence);

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

    function hide(string $code): bool
    {
        $essence = $this->setAttributeByCode($code);

        $result = ($this->getAttributeLocation())->hide($essence);

        $this->setAttribute($result, $essence);

        return $result;

    }

    function write(string $code): bool
    {
        $essence = $this->setAttributeByCode($code);

        $resultData = Attribute::GetDefaultAttribute();

        $result = ($this->getAttributeLocation())->write($essence, $resultData);

        $this->setAttribute($result, $resultData);

        return $result;

    }

    function read(string $code): bool
    {
        $attribute = $this->setAttributeByCode($code);

        $result = ($this->getAttributeSource())->read($attribute);

        $this->setAttribute($result, $attribute);

        return $result;

    }

    private function getAttributeSource(): AttributeSource
    {

        $repository = new AttributeSource($this->dataSource, $this->dataPath);

        return $repository;
    }

    function retrieveData(): IAttribute
    {
        $essence = $this->attribute->GetAttributeCopy();

        return $essence;
    }
}
