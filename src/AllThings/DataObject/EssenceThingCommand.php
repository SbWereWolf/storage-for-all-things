<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 21:48
 */


namespace AllThings\DataObject;


use Slim\Http\Request;

class EssenceThingCommand implements IEssenceThingCommand
{

    private $essenceIdentifier = '';
    private $thingIdentifier = '';

    public function __construct(Request $request, array $arguments)
    {
        $isExists = array_key_exists('essence-code', $arguments);
        if ($isExists) {
            $this->essenceIdentifier = $arguments['essence-code'];
        }

        $isExists = array_key_exists('thing-code', $arguments);
        if ($isExists) {
            $this->thingIdentifier = $arguments['thing-code'];
        }
    }

    public function getEssenceIdentifier()
    {
        $essenceIdentifier = $this->essenceIdentifier;

        return $essenceIdentifier;
    }

    public function getThingIdentifier()
    {
        $thingIdentifier = $this->thingIdentifier;

        return $thingIdentifier;
    }
}
