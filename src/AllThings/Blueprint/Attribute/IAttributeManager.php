<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 26.12.2021, 5:51
 */

namespace AllThings\Blueprint\Attribute;


use AllThings\DataAccess\Nameable\DataManager;
use AllThings\DataAccess\Retrievable;

interface IAttributeManager extends DataManager, Retrievable
{

    public function retrieveData(): IAttribute;

    public function getLocation(): string;

    public function getFormat(): string;

}
