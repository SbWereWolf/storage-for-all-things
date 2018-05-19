<?php
/**
 * Created by PhpStorm.
 * User: СЕРГЕЙ
 * Date: 19.05.2018
 * Time: 12:45
 */

namespace AllThings\Presentation;


use AllThings\Essence\EssenceEntity;

class ForEssenceEntity implements ForNamed,ForEssence
{
    private $entity = null;

    public function __construct(EssenceEntity $entity)
    {
        $this->entity = $entity;
    }

    function toJson(): \string
    {
        $entity = $this->entity;

        $code = $entity->getCode();
        $title = $entity->getTitle();
        $remark = $entity->getRemark();
        $storage = $entity->getStorage();

        $data = array(
            'code' => $code,
            'title' => $title,
            'remark' => $remark,
            'storage' => $storage
        );

        $json = json_encode($data);

        return $json;
    }
}