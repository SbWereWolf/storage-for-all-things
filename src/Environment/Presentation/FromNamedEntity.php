<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 26.05.18 12:39
 */

namespace Environment\Presentation;


use AllThings\DataObject\Nameable;

class ForNameableEntity implements Jsonable
{
    private $nameable = null;

    function __construct(Nameable $nameable)
    {
        $this->nameable = $nameable;
    }

    public function toJson(): string
    {
        $entity = $this->nameable;

        $code = $entity->getCode();
        $title = $entity->getTitle();
        $remark = $entity->getRemark();

        $data = array(
            'code' => $code,
            'title' => $title,
            'remark' => $remark,
        );

        $json = json_encode($data);

        return $json;
    }
}
