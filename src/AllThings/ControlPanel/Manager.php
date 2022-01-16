<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 16.01.2022, 8:05
 */

namespace AllThings\ControlPanel;

use AllThings\ControlPanel\Relation\BlueprintFactory;
use AllThings\ControlPanel\Relation\CatalogFactory;
use AllThings\ControlPanel\Relation\SpecificationFactory;
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
     * @throws Exception
     */
    public function setupProduct(string $product, array $values = [])
    {
        $essence = $this->getCategoryOfProduct($product);
        $catalog = (new CatalogFactory($this->db))->make($essence);

        $catalog->attach($product);

        $specification = (new SpecificationFactory($this->db))
            ->make($product);

        $features = array_keys($values);
        $specification->attach($features);

        if ($values) {
            $specification->define($values);
        }
    }

    /**
     * @throws Exception
     */
    public function updateProduct(string $product, array $values)
    {
        $specification = (new SpecificationFactory($this->db))
            ->make($product);
        $specification->define($values);
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

        $catalog = (new CatalogFactory($this->db))->make($category);
        $products = $catalog->list();
        foreach ($products as $product) {
            $specification = (new SpecificationFactory($this->db))
                ->make($product);
            $specification->attach([$feature]);

            if ($default) {
                $specification->define([$feature => $default]);
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

        $manager = new StorageManager($this->db, $category);
        $manager->prune($feature);
    }

    /**
     * @throws Exception
     */
    public function deleteProduct(string $product): bool
    {
        $essence = $this->getCategoryOfProduct($product);
        $catalog = (new CatalogFactory($this->db))->make($essence);

        $catalog->detach($product);

        $blueprint = (new BlueprintFactory($this->db))->make($essence);
        $features = $blueprint->list();

        $specification = (new SpecificationFactory($this->db))
            ->make($product);
        $specification->purge($features);

        $manager = new NamedManager($this->db, 'thing');
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $result = $manager->remove($product);

        return $result;
    }

    /**
     * @throws Exception
     */
    public function deleteCategory(string $category): bool
    {
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
}