<?php


namespace AllThings\Presentation;


use AllThings\Essence\IEssence;

class FromEssenceEntity implements Jsonable
{
    private $entity = null;

    function __construct(IEssence $entity)
    {
        $this->entity = $entity;
    }

    public function toJson(): \string
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
