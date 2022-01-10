<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 10.01.2022, 6:49
 */

namespace AllThings\ControlPanel;

use AllThings\Blueprint\Attribute\Attribute;
use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\Blueprint\Essence\Essence;
use AllThings\Blueprint\Essence\EssenceManager;
use AllThings\Blueprint\Essence\IEssence;
use AllThings\Blueprint\Specification\SpecificationManager;
use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Crossover\CrossoverManager;
use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\Linkage;
use AllThings\DataAccess\Linkage\LinkageManager;
use AllThings\DataAccess\Linkage\LinkageTable;
use AllThings\DataAccess\Nameable\Nameable;
use AllThings\DataAccess\Nameable\NamedEntity;
use AllThings\DataAccess\Nameable\NamedEntityManager;
use AllThings\SearchEngine\Searchable;
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

    /**
     * @throws Exception
     */
    public function createBlueprint(
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

    /**
     * @throws Exception
     */
    public function createKind(
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

        $handler = new AttributeManager(
            $code,
            'attribute',
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
        $handler->setSubject($attribute);

        $isSuccess = $handler->correct();
        if (!$isSuccess) {
            throw new Exception('Attribute must be updated with success');
        }

        return $attribute;
    }

    /**
     * @throws Exception
     */
    public function attachKind(string $essence, string $kind): Operator
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
        $linkage = (new Linkage())
            ->setLeftValue($essence)
            ->setRightValue($kind);
        $isSuccess = $manager->attach($linkage);

        if (!$isSuccess) {
            throw new Exception(
                'Attribute must be attached with success'
            );
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function detachKind(string $essence, string $kind): Operator
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
        $linkage = (new Crossover())
            ->setLeftValue($essence)
            ->setRightValue($kind);
        $isSuccess = $manager->detach($linkage);

        if (!$isSuccess) {
            throw new Exception(
                'Attribute must be detached with success'
            );
        }

        return $this;
    }

    /**
     * @param string $essence
     * @param string $code
     * @param string $title
     * @param string $description
     *
     * @return Nameable
     * @throws Exception
     */
    public function createItem(
        string $essence,
        string $code,
        string $title = '',
        string $description = '',
    ): Nameable
    {
        $nameable = (new NamedEntity())->setCode($code);
        $thingManager = new NamedEntityManager($code, 'thing', $this->db);

        $isSuccess = $thingManager->create();
        if (!$isSuccess) {
            throw new Exception("Thing must be created with success");
        }

        if ($title) {
            $nameable->setTitle($title);
        }
        if ($description) {
            $nameable->setRemark($description);
        }
        $thingManager->setSubject($nameable);
        if ($title || $description) {
            $isSuccess = $thingManager->correct();
        }
        if (!$isSuccess) {
            throw new Exception("Thing must be updated with success");
        }

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

        $specificationManager = new LinkageManager(
            $this->db,
            $specification,
            $essenceKey,
            $attributeKey,
        );
        $linkage = (new Linkage())->setLeftValue($essence);

        $isSuccess = $specificationManager->getAssociated($linkage);
        if (!$isSuccess) {
            throw new Exception(
                'Attributes of essence'
                . ' must be fetched with success'
            );
        }
        $isSuccess = $specificationManager->has();
        if (!$isSuccess) {
            throw new Exception("Essence must be linked to some attributes");
        }
        $attributes = $specificationManager->retrieveData();
        foreach ($attributes as $attribute) {
            $content = (new Linkage())
                ->setLeftValue($code)
                ->setRightValue($attribute);

            $table = SpecificationManager::getLocation(
                $attribute,
                $this->db,
            );
            $thingKey = new ForeignKey(
                'thing',
                'id',
                'code'
            );
            $attributeKey = new ForeignKey(
                'attribute',
                'id',
                'code'
            );
            $contentTable = new LinkageTable(
                $table,
                'thing_id',
                'attribute_id',
            );

            $contentManager = new CrossoverManager(
                $this->db,
                $contentTable,
                $thingKey,
                $attributeKey,
            );

            $isSuccess = $contentManager->attach($content);
            if (!$isSuccess) {
                throw new Exception(
                    "Attribute must be defined"
                    . " for thing with success"
                );
            }
        }

        $essenceKey = new ForeignKey(
            'essence',
            'id',
            'code'
        );
        $thingKey = new ForeignKey(
            'thing',
            'id',
            'code'
        );
        $catalogTable = new LinkageTable(
            'essence_thing',
            'essence_id',
            'thing_id',
        );

        $specificationManager = new LinkageManager(
            $this->db,
            $catalogTable,
            $essenceKey,
            $thingKey,
        );

        $linkage = (new Linkage())
            ->setLeftValue($essence)
            ->setRightValue($code);
        $isSuccess = $specificationManager->attach($linkage);
        if (!$isSuccess) {
            throw new Exception(
                "Thing `$code` must be linked"
                . " to essence `$essence` with success"
            );
        }

        return $nameable;
    }

    public function removeItem(
        string $essence,
        string $thing,
    ): bool {
        foreach (Searchable::DATA_LOCATION as $table) {
            $thingKey = new ForeignKey('thing', 'id', 'code');
            $attributeKey = new ForeignKey('attribute', 'id', 'code');
            $contentTable = new LinkageTable(
                $table,
                'thing_id',
                'attribute_id',
            );

            $contentManager = new CrossoverManager(
                $this->db,
                $contentTable,
                $thingKey,
                $attributeKey,
            );

            $content = (new Linkage())->setLeftValue($thing);
            $contentManager->detach($content);
        }

        $essenceKey = new ForeignKey('essence', 'id', 'code');
        $thingKey = new ForeignKey('thing', 'id', 'code');
        $catalogTable = new LinkageTable(
            'essence_thing',
            'essence_id',
            'thing_id',
        );
        $catalogManager = new LinkageManager(
            $this->db,
            $catalogTable,
            $essenceKey,
            $thingKey,
        );

        $linkage = (new Linkage())
            ->setLeftValue($essence)
            ->setRightValue($thing);
        $isSuccess = $catalogManager->detach($linkage);
        if (!$isSuccess) {
            throw new Exception(
                "Thing `$thing` and essence `$essence`"
                . ' must be detached with success'
            );
        }

        $handler = new NamedEntityManager($thing, 'thing', $this->db);

        $isSuccess = $handler->remove();
        if (!$isSuccess) {
            throw new Exception("Thing must be removed with success");
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function changeContent(
        string $thing,
        string $attribute,
        string $content
    ) {
        $value = (new Crossover())
            ->setContent($content);
        $value->setLeftValue($thing)
            ->setRightValue($attribute);

        $table = SpecificationManager::getLocation(
            $attribute,
            $this->db,
        );
        $thingKey = new ForeignKey(
            'thing',
            'id',
            'code'
        );
        $attributeKey = new ForeignKey(
            'attribute',
            'id',
            'code'
        );
        $contentTable = new LinkageTable(
            $table,
            'thing_id',
            'attribute_id',
        );

        $contentManager = new CrossoverManager(
            $this->db,
            $contentTable,
            $thingKey,
            $attributeKey,
        );

        $contentManager->setSubject($value);

        $isSuccess = $contentManager->store($value);
        if (!$isSuccess) {
            throw new Exception(
                'Attribute of thing'
                . ' must be defined with success'
            );
        }
    }

    /**
     * @throws Exception
     */
    public function expandItem(
        string $thing,
        string $attribute,
        string $value
    ): Operator {
        $table = SpecificationManager::getLocation(
            $attribute,
            $this->db,
        );
        $thingKey = new ForeignKey(
            'thing',
            'id',
            'code'
        );
        $attributeKey = new ForeignKey(
            'attribute',
            'id',
            'code'
        );
        $contentTable = new LinkageTable(
            $table,
            'thing_id',
            'attribute_id',
        );

        $manager = new CrossoverManager(
            $this->db,
            $contentTable,
            $thingKey,
            $attributeKey,
        );

        $content = (new Crossover());
        $content->setLeftValue($thing)
            ->setRightValue($attribute);

        $isSuccess = $manager->attach($content);
        if (!$isSuccess) {
            throw new Exception(
                'Attribute must be defined'
                . ' for thing with success'
            );
        }

        $content->setContent($value);
        $manager->setSubject($content);

        $isSuccess = $manager->store($content);
        if (!$isSuccess) {
            throw new Exception(
                'Content must be assign'
                . ' with success'
            );
        }

        return $this;
    }
}