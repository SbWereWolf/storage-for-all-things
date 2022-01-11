<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 12.01.2022, 3:00
 */

namespace AllThings\ControlPanel;

use AllThings\Blueprint\Attribute\AttributeHelper;
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
use Exception;
use PDO;

class Operator
{
    private PDO $db;
    private string $thing;

    /**
     * @param PDO    $connection
     * @param string $thing
     */
    public function __construct(PDO $connection, string $thing)
    {
        $this->db = $connection;
        $this->thing = $thing;
    }

    /**
     * @param string $essence
     * @param string $title
     * @param string $description
     *
     * @return Nameable
     * @throws Exception
     */
    public function create(
        string $essence,
        string $title = '',
        string $description = '',
    ): Nameable {
        $nameable = (new NamedEntity())->setCode($this->thing);
        $thingManager = new NamedEntityManager(
            $this->thing,
            'thing',
            $this->db
        );

        $isSuccess = $thingManager->create();
        if (!$isSuccess) {
            throw new Exception(
                'Thing must be created with success'
            );
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
            throw new Exception(
                'Thing must be updated with success'
            );
        }

        $specificationManager = $this->getSpecificationManager();
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
            throw new Exception(
                'Essence must be linked to some attributes'
            );
        }
        $attributes = $specificationManager->retrieveData();
        foreach ($attributes as $attribute) {
            $content = (new Linkage())
                ->setLeftValue($this->thing)
                ->setRightValue($attribute);

            $table = AttributeHelper::getLocation(
                $attribute,
                $this->db,
            );
            $contentManager = $this->getContentManager($table);

            $isSuccess = $contentManager->attach($content);
            if (!$isSuccess) {
                throw new Exception(
                    "Attribute must be defined"
                    . " for thing with success"
                );
            }
        }

        $catalogManager = $this->getCatalogManager();

        $linkage = (new Linkage())
            ->setLeftValue($essence)
            ->setRightValue($this->thing);
        $isSuccess = $catalogManager->attach($linkage);
        if (!$isSuccess) {
            throw new Exception(
                "Thing `$this->thing` must be linked"
                . " to essence `$essence` with success"
            );
        }

        return $nameable;
    }

    public function remove(
        string $essence,
    ): bool {
        foreach (Searchable::DATA_LOCATION as $table) {
            $contentManager = $this->getContentManager($table);
            $content = (new Linkage())->setLeftValue($this->thing);

            $contentManager->detach($content);
        }

        $catalogManager = $this->getCatalogManager();

        $linkage = (new Linkage())
            ->setLeftValue($essence)
            ->setRightValue($this->thing);
        $isSuccess = $catalogManager->detach($linkage);
        if (!$isSuccess) {
            throw new Exception(
                "Thing `$this->thing` and essence `$essence`"
                . ' must be detached with success'
            );
        }

        $handler = new NamedEntityManager($this->thing, 'thing', $this->db);

        $isSuccess = $handler->remove();
        if (!$isSuccess) {
            throw new Exception("Thing must be removed with success");
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function define(array $definition): static
    {
        foreach ($definition as $attribute => $content) {
            $this->defineOne($attribute, $content);
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function expand(
        string $attribute,
        string $value
    ): Operator {
        $table = AttributeHelper::getLocation(
            $attribute,
            $this->db,
        );
        $manager = $this->getContentManager($table);
        $content = (new Crossover());
        $content->setLeftValue($this->thing)
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

    /**
     * @throws Exception
     */
    private function defineOne(
        string $attribute,
        string $content
    ): static {
        $table = AttributeHelper::getLocation(
            $attribute,
            $this->db,
        );
        $contentManager = $this->getContentManager($table);

        $value = (new Crossover())
            ->setContent($content);
        $value->setLeftValue($this->thing)
            ->setRightValue($attribute);
        $contentManager->setSubject($value);

        $isSuccess = $contentManager->store($value);
        if (!$isSuccess) {
            throw new Exception(
                'Attribute of thing'
                . ' must be defined with success'
            );
        }

        return $this;
    }

    /**
     * @return LinkageManager
     */
    private function getCatalogManager(): LinkageManager
    {
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
        return $catalogManager;
    }

    /**
     * @param string $table
     *
     * @return CrossoverManager
     */
    private function getContentManager(string $table): CrossoverManager
    {
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
        return $contentManager;
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
        $catalogManager = new LinkageManager(
            $this->db,
            $specification,
            $essenceKey,
            $attributeKey,
        );
        return $catalogManager;
    }
}