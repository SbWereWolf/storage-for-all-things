<?php
/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:06
 */

namespace AllThings\Reception;

use AllThings\DataObject\EssenceUpdateCommand;
use AllThings\DataObject\IEssenceUpdateCommand;
use AllThings\Essence\EssenceEntity;
use Slim\Http\Request;

class ForEssenceEntity implements ForEssence
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

    function fromPut(): IEssenceUpdateCommand
    {
        $code = $this->arguments['code'];

        $request = $this->request;

        $body = $request->getParsedBody();

        $essence = EssenceEntity::GetDefaultExemplar();

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

        $parameter = EssenceEntity::GetDefaultExemplar();
        $parameter->setCode($code);

        $command = new EssenceUpdateCommand($parameter, $essence);

        return $command;
    }
}
