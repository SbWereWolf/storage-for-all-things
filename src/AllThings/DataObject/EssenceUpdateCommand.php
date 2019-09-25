<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 20.05.18 0:04
 */

namespace AllThings\DataObject;


use AllThings\Essence\IEssence;

class EssenceUpdateCommand implements IEssenceUpdateCommand
{
    private $parameter;
    private $subject;

    public function __construct(string $parameter, IEssence $subject)
    {
        $this->parameter = $parameter;
        $this->subject = $subject;
    }

    function getParameter(): string
    {
        $parameter = $this->parameter;

        return $parameter;
    }

    function getSubject(): IEssence
    {
        $result = $this->subject->GetEssenceCopy();

        return $result;
    }
}
