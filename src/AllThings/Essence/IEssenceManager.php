<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:33
 */

namespace AllThings\Essence;


use AllThings\DataAccess\Handler\DataManager;
use AllThings\DataAccess\Handler\Retrievable;

interface IEssenceManager extends DataManager, Retrievable
{

    function retrieveData(): IEssence;

}
