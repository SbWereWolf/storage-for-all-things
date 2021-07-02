<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:22
 */


namespace Environment\Command;


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
