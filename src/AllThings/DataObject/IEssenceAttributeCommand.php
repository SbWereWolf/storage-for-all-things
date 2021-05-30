<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 27.05.18 3:10
 */


namespace AllThings\DataObject;


interface IEssenceAttributeCommand
{
    public function getEssenceIdentifier();

    public function getAttributeIdentifier();

}
