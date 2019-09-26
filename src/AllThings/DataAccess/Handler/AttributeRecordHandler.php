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
use PDO;

class AttributeRecordHandler implements Valuable, Hideable, Retrievable
{

    private $storageLocation = 'attribute';
    private $dataSource = 'attribute';

    private $dataPath = null;
    private $attribute = null;

    function __construct(IAttribute $attribute, PDO $dataPath)
    {
        $this->attribute = $attribute;
        $this->dataPath = $dataPath;
    }

    function add(): bool
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

    function hide(): bool
    {
        $attribute = $this->attribute->GetAttributeCopy();

        $result = ($this->getAttributeLocation())->setIsHidden($attribute);

        $this->setAttribute($result, $attribute);

        return $result;

    }

    function write(string $code): bool
    {
        $target = $this->setAttributeByCode($code);

        $attribute = $this->attribute->GetAttributeCopy();

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

    function read(): bool
    {
        $target = $this->setAttributeByCode($code);

        $result = ($this->getAttributeSource())->select($target);

        $this->setAttribute($result, $target);

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
