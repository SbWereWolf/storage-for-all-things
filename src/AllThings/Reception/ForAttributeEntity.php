<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 1:08
 */

/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:06
 */

namespace AllThings\Reception;

use AllThings\DataObject\AttributeUpdateCommand;
use AllThings\DataObject\IAttributeUpdateCommand;
use AllThings\Essence\Attribute;
use Slim\Http\Request;

class ForAttributeEntity implements ForAttribute
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

    function fromPut(): IAttributeUpdateCommand
    {
        $code = $this->arguments['code'];
        $request = $this->request;
        $body = $request->getParsedBody();

        $attribute = Attribute::GetDefaultAttribute();

        $isCodeExists = array_key_exists('code', $body);
        if ($isCodeExists) {
            $code = $body['code'];
            $attribute->setCode($code);
        }
        $isTitleExists = array_key_exists('title', $body);
        if ($isTitleExists) {
            $title = $body['title'];
            $attribute->setTitle($title);
        }
        $isRemarkExists = array_key_exists('remark', $body);
        if ($isRemarkExists) {
            $remark = $body['remark'];
            $attribute->setRemark($remark);
        }
        $isDataTypeExists = array_key_exists('data_type', $body);
        if ($isDataTypeExists) {
            $dataType = $body['data_type'];
            $attribute->setDataType($dataType);
        }
        $isRangeTypeExists = array_key_exists('range_type', $body);
        if ($isRangeTypeExists) {
            $rangeType = $body['range_type'];
            $attribute->setRangeType($rangeType);
        }

        $parameter = Attribute::GetDefaultAttribute();
        $parameter->setCode($code);

        $command = new AttributeUpdateCommand($parameter, $attribute);

        return $command;
    }
}
