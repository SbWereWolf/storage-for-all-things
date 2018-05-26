<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 1:17
 */

namespace AllThings\DataObject;


use AllThings\Essence\IAttribute;

interface IAttributeUpdateCommand
{
    function getParameter(): \string;

    function getSubject(): IAttribute;

}
