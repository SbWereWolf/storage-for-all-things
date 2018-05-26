<?php
/**
 * storage-for-all-things
 * Copyright © 2018 Volkhin Nikolay
 * 23.05.18 1:23
 */

namespace AllThings\Presentation;


use AllThings\Essence\IAttribute;

class ForAttributeEntity implements ForAttribute
{
    private $attribute = null;

    function __construct(IAttribute $attribute)
    {
        $this->attribute = $attribute;
    }

    public function toJson(): \string
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
