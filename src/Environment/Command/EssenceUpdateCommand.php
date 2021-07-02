<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace Environment\Command;


use AllThings\Blueprint\Essence\IEssence;

class EssenceUpdateCommand implements IEssenceUpdateCommand
{
    private $parameter;
    private $subject;

    public function __construct(string $parameter, IEssence $subject)
    {
        $this->parameter = $parameter;
        $this->subject = $subject;
    }

    public function getParameter(): string
    {
        $parameter = $this->parameter;

        return $parameter;
    }

    public function getSubject(): IEssence
    {
        $result = $this->subject->GetEssenceCopy();

        return $result;
    }
}
