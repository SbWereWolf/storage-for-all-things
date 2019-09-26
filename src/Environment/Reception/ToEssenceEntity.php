<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:06
 */

namespace Environment\Reception;

use AllThings\DataObject\EssenceUpdateCommand;
use AllThings\DataObject\IEssenceUpdateCommand;
use AllThings\Essence\Essence;
use Slim\Http\Request;

class ToEssenceEntity implements ToEssence
{
    private $request = null;
    private $arguments = null;

    public function __construct(Request $request, array $arguments)
    {
        $this->request = $request;
        $this->arguments = $arguments;
    }

    function fromPost(): string
    {
        $code = $this->arguments['code'];

        return $code;
    }

    function fromGet(): string
    {
        $code = $this->arguments['code'];

        return $code;
    }

    function fromPut(): IEssenceUpdateCommand
    {
        $parameterCode = $this->arguments['code'];
        $parameter = Essence::GetDefaultEssence();
        $parameter->setCode($parameterCode);

        $request = $this->request;

        $body = $request->getParsedBody();

        $essence = Essence::GetDefaultEssence();

        $isCodeExists = array_key_exists('code', $body);
        if ($isCodeExists) {
            $code = $body['code'];
            $essence->setCode($code);
        }
        $isTitleExists = array_key_exists('title', $body);
        if ($isTitleExists) {
            $title = $body['title'];
            $essence->setTitle($title);
        }
        $isRemarkExists = array_key_exists('remark', $body);
        if ($isRemarkExists) {
            $remark = $body['remark'];
            $essence->setRemark($remark);
        }
        $isStoreAtExists = array_key_exists('store_at', $body);
        if ($isStoreAtExists) {
            $storeAt = $body['store_at'];
            $essence->setStoreAt($storeAt);
        }

        $command = new EssenceUpdateCommand($parameter, $essence);

        return $command;
    }
}
