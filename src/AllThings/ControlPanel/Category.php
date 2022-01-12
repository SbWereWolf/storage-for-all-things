<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 14:22
 */

namespace AllThings\ControlPanel;

use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\Linkage;
use AllThings\DataAccess\Linkage\LinkageManager;
use AllThings\DataAccess\Linkage\LinkageTable;
use Exception;
use PDO;

class Category
{
    private PDO $db;
    private string $essence;

    /**
     * @param PDO    $connection
     * @param string $essence
     */
    public function __construct(PDO $connection, string $essence)
    {
        $this->db = $connection;
        $this->essence = $essence;
    }

    public function attach(string $attribute): static
    {
        $manager = $this->getSpecificationManager();
        $linkage = (new Linkage())
            ->setRightValue($attribute)
            ->setLeftValue($this->essence);

        $isSuccess = $manager->attach($linkage);

        if (!$isSuccess) {
            throw new Exception(
                'Attribute must be attached with success'
            );
        }

        return $this;
    }

    public function detach(string $attribute): static
    {
        $manager = $this->getSpecificationManager();
        $linkage = (new Crossover())
            ->setRightValue($attribute)
            ->setLeftValue($this->essence);

        $isSuccess = $manager->detach($linkage);

        if (!$isSuccess) {
            throw new Exception(
                'Attribute must be detached with success'
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
}