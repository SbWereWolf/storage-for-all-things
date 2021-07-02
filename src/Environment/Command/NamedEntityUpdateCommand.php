<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace Environment\Command;


use AllThings\DataAccess\Nameable\Nameable;

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
