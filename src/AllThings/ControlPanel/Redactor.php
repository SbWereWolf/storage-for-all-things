<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 13.01.2022, 13:52
 */

namespace AllThings\ControlPanel;

use AllThings\Blueprint\Attribute\Attribute;
use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\Blueprint\Essence\Essence;
use AllThings\Blueprint\Essence\EssenceManager;
use AllThings\Blueprint\Essence\IEssence;
use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\LinkageManager;
use AllThings\DataAccess\Linkage\LinkageTable;
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

    public function makeCategory(string $essence): EssenceRelated
    {
        $essenceKey = new ForeignKey(
            'essence',
            'id',
            'code'
        );
        $attributeKey = new ForeignKey(
            'attribute',
            'id',
            'code'
        );
        $categoryTable = new LinkageTable(
            'essence_attribute',
            'essence_id',
            'attribute_id',
        );
        $details = new LinkageManager(
            $this->db,
            $categoryTable,
            $essenceKey,
            $attributeKey,
        );

        $category = new EssenceRelated($essence, $details,);

        return $category;
    }

    public function essence(
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
        $handler->setEssence($essence);

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
        $attributeManager->setAttribute($attribute);

        $isSuccess = $attributeManager->correct();
        if (!$isSuccess) {
            throw new Exception(
                'Attribute must be updated with success'
            );
        }

        return $attribute;
    }
}