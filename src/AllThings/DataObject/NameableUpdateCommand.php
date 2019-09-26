<?php
/**
 * storage-for-all-things
 * Copyright © 2019 Volkhin Nikolay
 * 26.09.2019, 22:24
 */

namespace AllThings\DataObject;


interface NameableUpdateCommand
{
    function getParameter(): string;

    function getSubject(): Nameable;

}
