<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 13:44
 */

namespace Environment\Presentation;


use AllThings\Attribute\IEssence;

class FromEssenceEntity implements Jsonable
{
    private $entity = null;

    public function __construct(IEssence $entity)
    {
        $this->entity = $entity;
    }

    public function toJson(): string
    {
        $entity = $this->entity;

        $code = $entity->getCode();
        $title = $entity->getTitle();
        $remark = $entity->getRemark();
        $storeAt = $entity->getStoreAt();

        $data = array(
            'code' => $code,
            'title' => $title,
            'remark' => $remark,
            'store_at' => $storeAt
        );

        $json = json_encode($data);

        return $json;
    }
}
