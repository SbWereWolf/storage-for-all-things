<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:44
 */

namespace AllThings\Attribute;


use AllThings\DataAccess\Handler\Retrievable;
use AllThings\DataAccess\Manager\DataManager;

interface IAttributeManager extends DataManager, Retrievable
{

    public function retrieveData(): IAttribute;

}
