<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 03.06.18 15:11
 */

namespace AllThings\DataObject;


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
