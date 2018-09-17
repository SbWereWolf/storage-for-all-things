<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:06
 */

namespace AllThings\Reception;

use Slim\Http\Request;

class ToNameableEntity implements ToNamed
{
    private $request = null;
    private $arguments = null;

    public function __construct(Request $request, array $arguments)
    {
        $this->request = $request;
        $this->arguments = $arguments;
    }

    function fromPost(): \string
    {
        $code = $this->arguments['code'];

        return $code;
    }

    function fromGet(): \string
    {
        $code = $this->arguments['code'];

        return $code;
    }
}
