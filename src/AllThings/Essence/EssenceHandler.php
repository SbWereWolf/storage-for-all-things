<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:33
 */

namespace AllThings\Essence;


use AllThings\DataAccess\Implementation\DataHandler;
use AllThings\DataAccess\Implementation\Retrievable;

interface EssenceHandler extends DataHandler,Retrievable
{

    function retrieveData(): IEssence;

}
