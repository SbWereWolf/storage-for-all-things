<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 15:01
 */

namespace AllThings\ControlPanel;

use AllThings\Blueprint\Attribute\Attribute;
use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\Blueprint\Essence\Essence;
use AllThings\Blueprint\Essence\EssenceManager;
use AllThings\Blueprint\Essence\IEssence;
use AllThings\StorageEngine\Storable;
use Exception;
use PDO;

class Redactor
{
    private PDO $db;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    public function create(
        string $code,
        string $title = '',
        string $description = '',
        string $storageKind = Storable::DIRECT_READING
    ): IEssence {
        $essence = Essence::GetDefaultEssence();
        $essence->setCode($code);

        $handler = new EssenceManager(
            $code,
            'essence',
            $this->db,
        );
        $isSuccess = $handler->create();
        if (!$isSuccess) {
            throw new Exception('Essence must be created with success');
        }

        if ($storageKind) {
            $essence->setStorageKind($storageKind);
        }
        if ($title) {
            $essence->setTitle($title);
        }
        if ($description) {
            $essence->setRemark($description);
        }
        $handler->setSubject($essence);

        if ($storageKind || $title || $description) {
            $isSuccess = $handler->correct();
        }
        if (!$isSuccess) {
            throw new Exception('Essence must be updated with success');
        }

        return $essence;
    }

    public function attribute(
        string $code,
        string $dataType,
        string $rangeType,
        string $title = '',
        string $description = ''
    ): IAttribute {
        $attribute = Attribute::GetDefaultAttribute();
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $attribute->setCode($code)
            ->setDataType($dataType)
            ->setRangeType($rangeType);

        $attributeManager = new AttributeManager(
            $code,
            'attribute',
            $this->db
        );

        $isSuccess = $attributeManager->create();
        if (!$isSuccess) {
            throw new Exception(
                'Attribute must be created with success'
            );
        }

        $attribute->setDataType($dataType)
            ->setRangeType($rangeType);

        if ($title) {
            $attribute->setTitle($title);
        }
        if ($description) {
            $attribute->setRemark($description);
        }
        $attributeManager->setSubject($attribute);

        $isSuccess = $attributeManager->correct();
        if (!$isSuccess) {
            throw new Exception(
                'Attribute must be updated with success'
            );
        }

        return $attribute;
    }
}