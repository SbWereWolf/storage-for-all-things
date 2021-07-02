<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:22
 */

namespace Environment\Command;


use AllThings\DataObject\ICrossover;

class ContentUpdateCommand implements IContentUpdateCommand
{
    private $parameter;
    private $subject;

    public function __construct(ICrossover $target, ICrossover $subject)
    {
        $this->parameter = $target;
        $this->subject = $subject;
    }

    public function getParameter(): ICrossover
    {
        $parameter = $this->parameter;

        return $parameter;
    }

    public function getSubject(): ICrossover
    {
        $result = $this->subject->getCrossoverCopy();

        return $result;
    }
}
