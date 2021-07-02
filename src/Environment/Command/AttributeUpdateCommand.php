<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:22
 */

namespace Environment\Command;


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

    public function getParameter(): string
    {
        $parameter = $this->parameter;

        return $parameter;
    }

    public function getSubject(): IAttribute
    {
        $result = $this->subject->GetAttributeCopy();

        return $result;
    }
}
