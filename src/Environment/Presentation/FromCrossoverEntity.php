<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace Environment\Presentation;


use AllThings\DataAccess\Crossover\ICrossover;

class FromCrossoverEntity implements Jsonable
{
    private $crossover = null;

    public function __construct(ICrossover $crossover)
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
