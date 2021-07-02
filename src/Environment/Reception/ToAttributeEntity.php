<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */


namespace Environment\Reception;

use AllThings\Blueprint\Attribute\Attribute;
use Environment\Command\AttributeUpdateCommand;
use Environment\Command\IAttributeUpdateCommand;
use Slim\Http\Request;

class ToAttributeEntity implements ToAttribute
{
    private $request = null;
    private $arguments = null;

    public function __construct(Request $request, array $arguments)
    {
        $this->request = $request;
        $this->arguments = $arguments;
    }

    public function fromPost(): string
    {
        $code = $this->arguments['code'];

        return $code;
    }

    public function fromGet(): string
    {
        $code = $this->arguments['code'];

        return $code;
    }

    public function fromPut(): IAttributeUpdateCommand
    {
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

        $parameter = $this->arguments['code'];
        $command = new AttributeUpdateCommand($parameter, $attribute);

        return $command;
    }
}
