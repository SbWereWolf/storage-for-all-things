<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 03.07.2021, 17:12
 */

namespace AllThings\ControlPanel;


use AllThings\Blueprint\Attribute\Attribute;
use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\Blueprint\Essence\Essence;
use AllThings\Blueprint\Essence\EssenceManager;
use AllThings\Blueprint\Essence\IEssence;
use AllThings\Blueprint\Specification\SpecificationManager;
use AllThings\StorageEngine\Storable;
use Exception;
use PDO;

class Operator
{
    private PDO $db;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    public function createBlueprint(
        string $code,
        string $storageKind = Storable::DIRECT_READING,
        string $title = '',
        string $description = ''
    ): IEssence {
        $essence = Essence::GetDefaultEssence();
        $essence->setCode($code);

        $handler = new EssenceManager(
            $essence,
            $this->db
        );
        $isSuccess = $handler->create();
        if (!$isSuccess) {
            throw new Exception('Essence must be created with success');
        }

        if ($storageKind) {
            $essence->setStorage($storageKind);
        }
        if ($title) {
            $essence->setTitle($title);
        }
        if ($description) {
            $essence->setRemark($description);
        }
        if ($storageKind || $title || $description) {
            $isSuccess = $handler->correct();
        }
        if (!$isSuccess) {
            throw new Exception('Essence must be updated with success');
        }

        return $essence;
    }

    public function createKind(
        string $code,
        string $dataType,
        string $rangeType,
        string $title = '',
        string $description = ''
    ): IAttribute {
        $attribute = Attribute::GetDefaultAttribute();
        $attribute->setCode($code)
            ->setDataType($dataType)
            ->setRangeType($rangeType);

        $handler = new AttributeManager(
            $attribute,
            $this->db
        );

        $isSuccess = $handler->create();
        if (!$isSuccess) {
            throw new Exception('Attribute must be created with success');
        }

        $attribute->setDataType($dataType)
            ->setRangeType($rangeType);

        if ($title) {
            $attribute->setTitle($title);
        }
        if ($description) {
            $attribute->setRemark($description);
        }

        $isSuccess = $handler->correct();
        if (!$isSuccess) {
            throw new Exception('Attribute must be updated with success');
        }

        return $attribute;
    }

    public function attachKind(string $kind, string $essence)
    {
        $manager = new SpecificationManager(
            $essence,
            $kind,
            $this->db
        );
        $isSuccess = $manager->linkUp();

        if (!$isSuccess) {
            throw new Exception('Attribute must be linked with success');
        }

        return $this;
    }
}