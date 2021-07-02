<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace Environment\Presentation;


use AllThings\Blueprint\Essence\IEssence;

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
