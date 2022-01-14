<?php
/*
 * storage-for-all-things
 * Copyright © 2022 Volkhin Nikolay
 * 14.01.2022, 6:20
 */

namespace AllThings\ControlPanel;

use AllThings\ControlPanel\Relation\BlueprintFactory;
use AllThings\ControlPanel\Relation\CatalogFactory;
use AllThings\DataAccess\Linkage\ForeignKey;
use AllThings\DataAccess\Linkage\Linkage;
use AllThings\DataAccess\Linkage\LinkageManager;
use AllThings\DataAccess\Linkage\LinkageTable;
use AllThings\DataAccess\Nameable\NamedManager;
use AllThings\SearchEngine\Searchable;
use AllThings\StorageEngine\StorageManager;
use PDO;

class Manager
{
    private PDO $db;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;
    }

    public function setupCategory(string $category, array $features)
    {
        $blueprint = (new BlueprintFactory($this->db))->make($category);
        foreach ($features as $feature) {
            $blueprint->attach($feature);
        }
    }

    public function setupProduct(string $product, array $values = [])
    {
        $essence = $this->getCategoryOfProduct($product);
        $catalog = (new CatalogFactory($this->db))->make($essence);

        $catalog->attach($product);

        $specification = $this->makeSpecification($product);

        $features = array_keys($values);
        $specification->attach($features);

        if ($values) {
            $specification->define($values);
        }
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
        $essence = $manager->getAssociated($linkage)[0];

        return $essence;
    }

    /**
     * @param string $product
     *
     * @return Specification
     */
    private function makeSpecification(string $product): Specification
    {
        $access = new ContentAccessFactory(
            $this->db,
            Searchable::DATA_LOCATION,
        );
        $specification = new Specification($product, $access);

        return $specification;
    }

    public function updateProduct(string $product, array $values)
    {
        $specification = $this->makeSpecification($product);
        $specification->define($values);
    }

    public function expandCategory(
        string $category,
        string $feature,
        string $default = '',
    ) {
        $blueprint = (new BlueprintFactory($this->db))
            ->make($category);
        $blueprint->attach($feature);

        $access = new ContentAccessFactory(
            $this->db,
            Searchable::DATA_LOCATION,
        );

        $catalog = (new CatalogFactory($this->db))->make($category);
        $products = $catalog->list();
        foreach ($products as $product) {
            $specification = new Specification($product, $access);
            $specification->attach([$feature]);

            if ($default) {
                $specification->define([$feature => $default]);
            }
        }
    }

    public function pruneCategory(
        string $category,
        string $feature,
    ) {
        $blueprint = (new BlueprintFactory($this->db))
            ->make($category);
        $blueprint->detach($feature);

        $access = new ContentAccessFactory(
            $this->db,
            Searchable::DATA_LOCATION,
        );

        $catalog = (new CatalogFactory($this->db))->make($category);
        $products = $catalog->list();
        foreach ($products as $product) {
            $specification = new Specification($product, $access);
            $specification->detach([$feature]);
        }

        $manager = new StorageManager($this->db, $category);
        $manager->prune($feature);
    }

    public function deleteProduct(string $product): bool
    {
        $essence = $this->getCategoryOfProduct($product);
        $catalog = (new CatalogFactory($this->db))->make($essence);

        $catalog->detach($product);

        $blueprint = (new BlueprintFactory($this->db))->make($essence);
        $features = $blueprint->list();

        $specification = $this->makeSpecification($product);
        $specification->detach($features);

        $manager = new NamedManager($product, 'thing', $this->db);
        $result = $manager->remove();

        return $result;
    }

    public function deleteCategory(string $category): bool
    {
        $blueprint = (new BlueprintFactory($this->db))->make($category);
        $features = $blueprint->list();

        $catalog = (new CatalogFactory($this->db))->make($category);
        $products = $catalog->list();
        foreach ($products as $product) {
            $specification = $this->makeSpecification($product);
            $specification->detach($features);

            $catalog->detach($product);

            $thing = new NamedManager($product, 'thing', $this->db);
            $thing->remove();
        }

        $manager = new NamedManager($category, 'essence', $this->db);
        $result = $manager->remove();


        return $result;
    }
}