<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 03.06.18 16:03
 */

namespace AllThings\Presentation;


use AllThings\DataObject\ICrossover;

class FromCrossoverEntity implements Jsonable
{
    private $crossover = null;

    function __construct(ICrossover $crossover)
    {
        $this->crossover = $crossover;
    }

    public function toJson(): string
    {
        $entity = $this->crossover;

        $thing = $entity->getLeftValue();
        $attribute = $entity->getRightValue();
        $content = $entity->getContent();

        $data = array(
            'thing' => $thing,
            'attribute' => $attribute,
            'content' => $content,
        );

        $json = json_encode($data);

        return $json;
    }
}
