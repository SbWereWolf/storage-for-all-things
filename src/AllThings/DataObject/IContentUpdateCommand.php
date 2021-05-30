<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 03.06.18 15:09
 */

namespace AllThings\DataObject;


interface IContentUpdateCommand
{
    public function getParameter(): ICrossover;

    public function getSubject(): ICrossover;
}
