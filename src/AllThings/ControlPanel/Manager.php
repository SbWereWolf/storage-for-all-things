<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 17.01.2022, 7:56
 */

namespace AllThings\ControlPanel;

use AllThings\ControlPanel\Relation\BlueprintFactory;
use AllThings\ControlPanel\Relation\CatalogFactory;
use AllThings\ControlPanel\Relation\SpecificationFactory;
use AllThings\DataAccess\Crossover\Crossover;
use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\Linkage;
use AllThings\DataAccess\Linkage\LinkageManager;
use AllThings\DataAccess\Linkage\LinkageTable;
use AllThings\DataAccess\Nameable\NamedManager;
use AllThings\StorageEngine\StorageManager;
use Exception;
use PDO;

class Manager
{
    private PDO $db;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    /**
     * @throws Exception
     */
    public function setupCategory(string $category, array $features)
    {
        $blueprint = (new BlueprintFactory($this->db))->make($category);
        foreach ($features as $feature) {
            $blueprint->attach($feature);
        }
    }

    /**
     * @param string $essence *
     *
     * @throws Exception
     */
    public function setupProduct(
        string $essence,
        string $product,
        array $values = []
    ) {
        $catalog = (new CatalogFactory($this->db))->make($essence);

        $catalog->attach($product);

        $specification = (new SpecificationFactory($this->db))
            ->make($product);

        $features = array_keys($values);
        $specification->attach($features);

        if ($values) {
            $specification->define($values);
        }

        $this->updateContent($essence, $product, $values);
    }

    /**
     * @throws Exception
     */
    public function updateProduct(string $product, array $values)
    {
        $specification = (new SpecificationFactory($this->db))
            ->make($product);
        $specification->define($values);

        $essence = $this->getCategoryOfProduct($product);
        $this->updateContent($essence, $product, $values);
    }

    /**
     * @throws Exception
     */
    public function expandCategory(
        string $category,
        string $feature,
        string $default = '',
    ) {
        $blueprint = (new BlueprintFactory($this->db))
            ->make($category);
        $blueprint->attach($feature);

        $schema = new StorageManager($this->db, $category,);
        $schema->setup($feature);

        $catalog = (new CatalogFactory($this->db))->make($category);
        $products = $catalog->list();

        foreach ($products as $product) {
            $specification = (new SpecificationFactory($this->db))
                ->make($product);
            $specification->attach([$feature]);

            if ($default) {
                $values = [$feature => $default];

                $specification->define($values);
                $this->updateContent($category, $product, $values);
            }
        }

    }

    /**
     * @throws Exception
     */
    public function pruneCategory(
        string $category,
        string $feature,
    ) {
        $blueprint = (new BlueprintFactory($this->db))
            ->make($category);
        $blueprint->detach($feature);

        $catalog = (new CatalogFactory($this->db))->make($category);
        $products = $catalog->list();
        foreach ($products as $product) {
            $specification = (new SpecificationFactory($this->db))
                ->make($product);
            $specification->detach([$feature]);
        }

        $schema = new StorageManager($this->db, $category);
        $schema->prune($feature);
    }

    /**
     * @throws Exception
     */
    public function deleteProduct(string $product): bool
    {
        $category = $this->getCategoryOfProduct($product);

        $catalog = (new CatalogFactory($this->db))->make($category);

        $catalog->detach($product);

        $features = (new BlueprintFactory($this->db))
            ->make($category)
            ->list();

        $specification = (new SpecificationFactory($this->db))
            ->make($product);
        $specification->purge($features);

        $manager = new NamedManager($this->db, 'thing');
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $manager->remove($product);

        $schema = new StorageManager($this->db, $category,);
        $schema->refresh();

        return $result;
    }

    /**
     * @throws Exception
     */
    public function deleteCategory(string $category): bool
    {
        $schema = new StorageManager($this->db, $category,);
        $schema->drop();

        $blueprint = (new BlueprintFactory($this->db))->make($category);
        $features = $blueprint->list();
        $blueprint->purge();

        $catalog = (new CatalogFactory($this->db))->make($category);
        $products = $catalog->list();
        foreach ($products as $product) {
            $specification = (new SpecificationFactory($this->db))
                ->make($product);
            $specification->purge($features);
        }

        $catalog->purge();
        foreach ($products as $product) {
            $thing = new NamedManager($this->db, 'thing',);
            $thing->remove($product);
        }

        $manager = new NamedManager($this->db, 'essence',);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $manager->remove($category);

        return $result;
    }

    /**
     * @param string $product
     *
     * @return string
     */
    private function getCategoryOfProduct(string $product): string
    {
        $leftKey = new ForeignKey('thing', 'id', 'code',);
        $rightKey = new ForeignKey('essence', 'id', 'code',);
        $table = new LinkageTable(
            'essence_thing',
            $leftKey,
            $rightKey,
        );
        $manager = new LinkageManager($this->db, $table);

        $linkage = (new Linkage())->setLeftValue($product);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $essence = $manager->getAssociated($linkage)[0];

        return $essence;
    }

    /**
     * @param string $essence
     * @param string $product
     * @param array  $values
     *
     * @throws Exception
     */
    private function updateContent(
        string $essence,
        string $product,
        array $values
    ): void {
        $data = [];
        foreach ($values as $attribute => $value) {
            $content = (new Crossover())->setContent($value);
            $content->setLeftValue($product)
                ->setRightValue($attribute);
            $data[] = $content;
        }

        $schema = new StorageManager($this->db, $essence,);
        $schema->refresh($data);
    }
}