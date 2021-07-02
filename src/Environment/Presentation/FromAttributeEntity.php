<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 02.07.2021, 16:47
 */

namespace Environment\Presentation;


use AllThings\Blueprint\Attribute\IAttribute;

class FromAttributeEntity implements Jsonable
{
    private $attribute = null;

    public function __construct(IAttribute $attribute)
    {
        $this->attribute = $attribute;
    }

    public function toJson(): string
    {
        $entity = $this->attribute;

        $code = $entity->getCode();
        $title = $entity->getTitle();
        $remark = $entity->getRemark();
        $dataType = $entity->getDataType();
        $rangeType = $entity->getRangeType();

        $data = array(
            'code' => $code,
            'title' => $title,
            'remark' => $remark,
            'data_type' => $dataType,
            'range_type' => $rangeType
        );

        $json = json_encode($data);

        return $json;
    }
}
