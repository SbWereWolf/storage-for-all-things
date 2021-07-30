<?php
/*
 * storage-for-all-things
 * Copyright © 2021 Volkhin Nikolay
 * 30.07.2021, 5:46
 */

namespace AllThings\ControlPanel;


use AllThings\Blueprint\Attribute\Attribute;
use AllThings\Blueprint\Attribute\AttributeManager;
use AllThings\Blueprint\Attribute\IAttribute;
use AllThings\Blueprint\Essence\Essence;
use AllThings\Blueprint\Essence\EssenceManager;
use AllThings\Blueprint\Essence\IEssence;
use AllThings\Blueprint\Specification\SpecificationManager;
use AllThings\Catalog\CatalogManager;
use AllThings\Content\ContentManager;
use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Nameable\Nameable;
use AllThings\DataAccess\Nameable\NamedEntity;
use AllThings\DataAccess\Nameable\NamedEntityManager;
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
            $essence->setStorageKind($storageKind);
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

    /**
     * @throws Exception
     */
    public function attachKind(string $essence, string $kind): Operator
    {
        $manager = new SpecificationManager(
            $this->db
        );
        $linkage = (new Crossover())
            ->setLeftValue($essence)
            ->setRightValue($kind);
        $isSuccess = $manager->linkUp($linkage);

        if (!$isSuccess) {
            throw new Exception('Attribute must be linked with success');
        }

        return $this;
    }

    /**
     * @param string $essence
     * @param string $code
     * @param string $title
     * @param string $description
     * @return Nameable
     * @throws Exception
     */
    public function createItem(
        string $essence,
        string $code,
        string $title = '',
        string $description = ''
    ): Nameable
    {
        $manager = new SpecificationManager($this->db);
        $linkage = (new Crossover())->setLeftValue($essence);
        $isSuccess = $manager->getAssociated($linkage);
        if (!$isSuccess) {
            throw new Exception('Attributes of essence'
                . ' must be fetched with success');
        }
        $isSuccess = $manager->has();
        if (!$isSuccess) {
            throw new Exception("Essence must be linked to some attributes");
        }
        $attributes = $manager->retrieveData();

        $nameable = (new NamedEntity())->setCode($code);
        $handler = new NamedEntityManager($nameable, 'thing', $this->db);

        $isSuccess = $handler->create();
        if (!$isSuccess) {
            throw new Exception("Thing must be created with success");
        }

        if ($title) {
            $nameable->setTitle($title);
        }
        if ($description) {
            $nameable->setRemark($description);
        }
        if ($title || $description) {
            $isSuccess = $handler->correct();
        }
        if (!$isSuccess) {
            throw new Exception("Thing must be updated with success");
        }

        foreach ($attributes as $attribute) {
            $content = (new Crossover())
                ->setLeftValue($code)
                ->setRightValue($attribute);
            $handler = new ContentManager($content, $this->db);

            $isSuccess = $handler->attach();
            if (!$isSuccess) {
                throw new Exception("Attribute must be defined"
                    . " for thing with success");
            }
        }

        $manager = new CatalogManager(
            $essence,
            $code,
            $this->db
        );
        $linkage = (new Crossover())
            ->setLeftValue($essence)
            ->setRightValue($code);
        $isSuccess = $manager->linkUp($linkage);
        if (!$isSuccess) {
            throw new Exception("Thing `$code` must be linked"
                . " to essence `$essence` with success");
        }

        return $nameable;
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
            ->setLeftValue($thing)
            ->setRightValue($attribute)
            ->setContent($content);

        $handler = new ContentManager($value, $this->db);

        $isSuccess = $handler->store($value);
        if (!$isSuccess) {
            throw new Exception('Attribute of thing'
                . ' must be defined with success');
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
        $content = (new Crossover())
            ->setLeftValue($thing)
            ->setRightValue($attribute);
        $manager = new ContentManager($content, $this->db);

        $isSuccess = $manager->attach();
        if (!$isSuccess) {
            throw new Exception("Attribute must be defined"
                . " for thing with success");
        }

        $content->setContent($value);
        $isSuccess = $manager->store($content);
        if (!$isSuccess) {
            throw new Exception("Content must be assign"
                . " with success");
        }

        return $this;
    }
}