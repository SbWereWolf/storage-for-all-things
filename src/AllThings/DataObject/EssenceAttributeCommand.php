<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 3:11
 */


namespace AllThings\DataObject;


use Slim\Http\Request;

class EssenceAttributeCommand implements IEssenceAttributeCommand
{

    private $essenceIdentifier = '';
    private $attributeIdentifier = '';

    public function __construct(Request $request, array $arguments)
    {
        $isExists = array_key_exists('essence-code', $arguments);
        if ($isExists) {
            $this->essenceIdentifier = $arguments['essence-code'];
        }

        $isExists = array_key_exists('attribute-code', $arguments);
        if ($isExists) {
            $this->attributeIdentifier = $arguments['attribute-code'];
        }
    }

    public function getEssenceIdentifier()
    {
        $essenceIdentifier = $this->essenceIdentifier;

        return $essenceIdentifier;
    }

    public function getAttributeIdentifier()
    {
        $attributeIdentifier = $this->attributeIdentifier;

        return $attributeIdentifier;
    }
}
