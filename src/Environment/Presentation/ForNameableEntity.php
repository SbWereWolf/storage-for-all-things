<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace Environment\Presentation;


use AllThings\DataAccess\Nameable\Nameable;

class ForNameableEntity implements Jsonable
{
    private $nameable = null;

    public function __construct(Nameable $nameable)
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
