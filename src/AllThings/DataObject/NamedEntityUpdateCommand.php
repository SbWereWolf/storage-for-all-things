<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 26.09.2019, 22:29
 */

namespace AllThings\DataObject;


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
