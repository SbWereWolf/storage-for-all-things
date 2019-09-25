<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 1:16
 */

namespace AllThings\DataObject;


use AllThings\Essence\IAttribute;

class AttributeUpdateCommand implements IAttributeUpdateCommand
{
    private $parameter;
    private $subject;

    public function __construct(string $parameter, IAttribute $subject)
    {
        $this->parameter = $parameter;
        $this->subject = $subject;
    }

    function getParameter(): string
    {
        $parameter = $this->parameter;

        return $parameter;
    }

    function getSubject(): IAttribute
    {
        $result = $this->subject->GetAttributeCopy();

        return $result;
    }
}
