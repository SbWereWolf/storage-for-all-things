<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 11.01.2022, 6:09
 */

namespace AllThings\ControlPanel;

use AllThings\Blueprint\Attribute\Attribute;
use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\Linkage;
use AllThings\DataAccess\Linkage\LinkageManager;
use AllThings\DataAccess\Linkage\LinkageTable;
use Exception;
use PDO;

class Redactor
{
    private PDO $db;
    private string $attribute;

    /**
     * @param PDO    $connection
     * @param string $attribute
     */
    public function __construct(PDO $connection, string $attribute)
    {
        $this->db = $connection;
        $this->attribute = $attribute;
    }

    /**
     * @param string   $dataType
     * @param string   $rangeType
     * @param string   $title
     * @param string   $description
     * @param Operator $this
     *
     * @return IAttribute
     * @throws Exception
     */
    public function create(
        string $dataType,
        string $rangeType,
        string $title = '',
        string $description = '',
    ): IAttribute {
        $attribute = Attribute::GetDefaultAttribute();
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $attribute->setCode($this->attribute)
            ->setDataType($dataType)
            ->setRangeType($rangeType);

        $attributeManager = new AttributeManager(
            $this->attribute,
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

    /**
     * @param string   $essence
     * @param Operator $this
     *
     * @return Operator
     * @throws Exception
     */
    public function attach(string $essence,): static
    {
        $manager = $this->getSpecificationManager();
        $linkage = (new Linkage())
            ->setLeftValue($essence)
            ->setRightValue($this->attribute);

        $isSuccess = $manager->attach($linkage);

        if (!$isSuccess) {
            throw new Exception(
                'Attribute must be attached with success'
            );
        }

        return $this;
    }

    /**
     * @return LinkageManager
     */
    private function getSpecificationManager(): LinkageManager
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
        $specification = new LinkageTable(
            'essence_attribute',
            'essence_id',
            'attribute_id',
        );
        $manager = new LinkageManager(
            $this->db,
            $specification,
            $essenceKey,
            $attributeKey,
        );

        return $manager;
    }

    /**
     * @param string   $essence
     * @param Operator $this
     *
     * @return Operator
     * @throws Exception
     */
    public function detach(string $essence,): static
    {
        $manager = $this->getSpecificationManager();
        $linkage = (new Crossover())
            ->setLeftValue($essence)
            ->setRightValue($this->attribute);

        $isSuccess = $manager->detach($linkage);

        if (!$isSuccess) {
            throw new Exception(
                'Attribute must be detached with success'
            );
        }

        return $this;
    }
}