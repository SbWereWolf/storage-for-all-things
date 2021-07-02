<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:22
 */

namespace Environment\Command;


use AllThings\DataObject\Nameable;

class NamedEntityUpdateCommand implements NameableUpdateCommand
{
    private $parameter;
    private $subject;

    public function __construct(string $parameter, Nameable $subject)
    {
        $this->parameter = $parameter;
        $this->subject = $subject;
    }

    public function getParameter(): string
    {
        $parameter = $this->parameter;

        return $parameter;
    }

    public function getSubject(): Nameable
    {
        $result = $this->subject->getNameableCopy();

        return $result;
    }
}
