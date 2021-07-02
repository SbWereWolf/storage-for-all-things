<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:22
 */

namespace Environment\Reception;


use AllThings\DataObject\Crossover;
use AllThings\DataObject\ICrossover;
use Environment\Command\ContentUpdateCommand;
use Environment\Command\IContentUpdateCommand;
use Slim\Http\Request;

class ToCrossoverEntity implements ToCrossover
{
    private $request = null;
    private $arguments = null;

    public function __construct(Request $request, array $arguments)
    {
        $this->request = $request;
        $this->arguments = $arguments;
    }

    public function fromPost(): ICrossover
    {
        $targetContent = $this->fromArguments();

        return $targetContent;
    }

    /**
     * @return ICrossover
     */
    private function fromArguments(): ICrossover
    {
        $leftValue = $this->arguments['thing-code'];
        $rightValue = $this->arguments['attribute-code'];
        $targetContent = (new Crossover())->setLeftValue($leftValue)->setRightValue($rightValue);
        return $targetContent;
    }

    public function fromGet(): ICrossover
    {
        $targetContent = $this->fromArguments();

        return $targetContent;
    }

    public function fromPut(): IContentUpdateCommand
    {
        $request = $this->request;

        $body = $request->getParsedBody();

        $contentEntity = new Crossover();

        $isThingExists = array_key_exists('thing', $body);
        if ($isThingExists) {
            $thing = $body['thing'];
            $contentEntity->setLeftValue($thing);
        }
        $isAttributeExists = array_key_exists('attribute', $body);
        if ($isAttributeExists) {
            $attribute = $body['attribute'];
            $contentEntity->setRightValue($attribute);
        }
        $isContentExists = array_key_exists('content', $body);
        if ($isContentExists) {
            $content = $body['content'];
            $contentEntity->setContent($content);
        }

        $targetContent = $this->fromArguments();
        $command = new ContentUpdateCommand($targetContent, $contentEntity);

        return $command;
    }

}
