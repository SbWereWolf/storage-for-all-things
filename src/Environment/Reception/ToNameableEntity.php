<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:22
 */

/**
 * storage-for-all-things
 * © Volkhin Nikolay M., 2018
 * Date: 18.05.2018 Time: 0:06
 */

namespace Environment\Reception;

use AllThings\DataObject\NamedEntity;
use Environment\Command\NameableUpdateCommand;
use Environment\Command\NamedEntityUpdateCommand;
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

    public function fromPut(): NameableUpdateCommand
    {
        $parameterCode = $this->arguments['code'];

        $request = $this->request;

        $body = $request->getParsedBody();

        $named = new NamedEntity();

        $isCodeExists = array_key_exists('code', $body);
        if ($isCodeExists) {
            $code = $body['code'];
            $named->setCode($code);
        }
        $isTitleExists = array_key_exists('title', $body);
        if ($isTitleExists) {
            $title = $body['title'];
            $named->setTitle($title);
        }
        $isRemarkExists = array_key_exists('remark', $body);
        if ($isRemarkExists) {
            $remark = $body['remark'];
            $named->setRemark($remark);
        }

        $command = new NamedEntityUpdateCommand($parameterCode, $named);

        return $command;
    }
}
